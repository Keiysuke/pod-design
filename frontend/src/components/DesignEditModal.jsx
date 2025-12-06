import { useState } from 'react'
import axios from 'axios'
import './DesignEditModal.css'

export default function DesignEditModal({ design, onClose, onUpdated, onDeleted }) {
    const [title, setTitle] = useState(design.title)
    const [description, setDescription] = useState(design.description || '')
    const [file, setFile] = useState(null)
    const [loading, setLoading] = useState(false)
    const [error, setError] = useState(null)

    const handleFileChange = (e) => setFile(e.target.files?.[0] ?? null)

    const handleUpdate = async (e) => {
        e.preventDefault()
        setLoading(true)
        setError(null)
        try {
            const payload = new FormData()
            payload.append('title', title)
            payload.append('description', description)
            if (file) payload.append('picture', file)

            // Use POST so multipart uploads work; backend accepts POST for update
            const res = await axios.post(`http://127.0.0.1:8000/api/designs/${design.id}`, payload)
            onUpdated?.(res.data.data)
            onClose?.()
        } catch (err) {
            setError(err.response?.data?.message || 'Update failed')
            console.error(err)
        } finally {
            setLoading(false)
        }
    }

    const handleDelete = async () => {
        if (!confirm('Are you sure you want to delete this design? This will remove the image file.')) return
        setLoading(true)
        try {
            await axios.delete(`http://127.0.0.1:8000/api/designs/${design.id}`)
            onDeleted?.(design.id)
            onClose?.()
        } catch (err) {
            setError(err.response?.data?.message || 'Delete failed')
            console.error(err)
        } finally {
            setLoading(false)
        }
    }

    return (
        <div className="modal-backdrop" onClick={(e) => e.target === e.currentTarget && onClose?.()}>
            <div className="modal">
                <header className="modal-header">
                    <h3>Edit Design</h3>
                    <button className="modal-close" onClick={() => onClose?.()}>&times;</button>
                </header>
                <form className="modal-body" onSubmit={handleUpdate}>
                    {error && <div className="error">{error}</div>}

                    <label>Title</label>
                    <input value={title} onChange={e => setTitle(e.target.value)} required />

                    <label>Description</label>
                    <textarea value={description} onChange={e => setDescription(e.target.value)} />

                    <label>Replace image (PNG/JPEG)</label>
                    <input type="file" accept="image/png, image/jpeg" onChange={handleFileChange} />

                    <div className="modal-actions">
                        <button type="submit" disabled={loading} className="btn btn-primary">{loading ? 'Saving...' : 'Save'}</button>
                        <button type="button" onClick={handleDelete} disabled={loading} className="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    )
}
