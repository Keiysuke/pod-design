import { useState } from 'react'
import axios from 'axios'
import './DesignForm.css'

export default function DesignForm({ onDesignCreated }) {
    const [formData, setFormData] = useState({
        title: '',
        description: '',
    })
    const [file, setFile] = useState(null)
    const [loading, setLoading] = useState(false)
    const [error, setError] = useState(null)
    const [success, setSuccess] = useState(false)

    const handleChange = (e) => {
        const { name, value } = e.target
        setFormData(prev => ({
            ...prev,
            [name]: value
        }))
    }

    const handleFileChange = (e) => {
        setFile(e.target.files?.[0] ?? null)
    }

    const handleSubmit = async (e) => {
        e.preventDefault()
        setLoading(true)
        setError(null)
        setSuccess(false)

        try {
            const payload = new FormData()
            payload.append('title', formData.title)
            if (formData.description) payload.append('description', formData.description)
            if (file) payload.append('picture', file)

            const response = await axios.post('http://127.0.0.1:8000/api/designs', payload)

            setSuccess(true)
            setFormData({ title: '', description: '' })
            setFile(null)

            if (onDesignCreated) onDesignCreated(response.data.data)

            // Clear success message after 3 seconds
            setTimeout(() => setSuccess(false), 3000)
        } catch (err) {
            setError(err.response?.data?.message || 'Error creating design')
            console.error('Error:', err)
        } finally {
            setLoading(false)
        }
    }

    return (
        <form className="design-form" onSubmit={handleSubmit}>
            <h2>Create New Design</h2>

            {error && <div className="alert alert-error">{error}</div>}
            {success && <div className="alert alert-success">Design created successfully!</div>}

            <div className="form-group">
                <label htmlFor="title">Title *</label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    value={formData.title}
                    onChange={handleChange}
                    required
                    placeholder="Enter design title"
                />
            </div>

            <div className="form-group">
                <label htmlFor="description">Description</label>
                <textarea
                    id="description"
                    name="description"
                    value={formData.description}
                    onChange={handleChange}
                    placeholder="Enter design description"
                    rows="4"
                />
            </div>

            <div className="form-group">
                <label htmlFor="picture">Upload image (png, jpg)</label>
                <input
                    type="file"
                    id="picture"
                    name="picture"
                    accept="image/png, image/jpeg"
                    onChange={handleFileChange}
                />
            </div>

            <button type="submit" disabled={loading}>
                {loading ? 'Creating...' : 'Create Design'}
            </button>
        </form>
    )
}
