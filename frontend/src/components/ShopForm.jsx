import { useState } from 'react'
import axios from 'axios'

export default function ShopForm({ onShopCreated }) {
    const [form, setForm] = useState({ name: '', url: '' })
    const [file, setFile] = useState(null)
    const [loading, setLoading] = useState(false)
    const [error, setError] = useState(null)

    const handleChange = (e) => setForm(prev => ({ ...prev, [e.target.name]: e.target.value }))
    const handleFileChange = (e) => setFile(e.target.files?.[0] ?? null)

    const handleSubmit = async (e) => {
        e.preventDefault()
        setLoading(true)
        setError(null)
        try {
            const payload = new FormData()
            payload.append('name', form.name)
            payload.append('url', form.url)
            if (file) payload.append('logo', file)

            const res = await axios.post('http://127.0.0.1:8000/api/shops', payload)
            setForm({ name: '', url: '' })
            setFile(null)
            onShopCreated?.(res.data.data)
        } catch (err) {
            const errorMsg = err.response?.data?.message || 'Error creating shop'
            setError(errorMsg)
            console.error('Error details:', err.response?.data)
        } finally {
            setLoading(false)
        }
    }

    return (
        <form className="shop-form" onSubmit={handleSubmit}>
            <h2>Create Shop</h2>
            {error && <div className="alert alert-error">{error}</div>}

            <div className="form-group">
                <label>Name *</label>
                <input name="name" value={form.name} onChange={handleChange} required />
            </div>

            <div className="form-group">
                <label>URL *</label>
                <input name="url" value={form.url} onChange={handleChange} required />
            </div>

            <div className="form-group">
                <label>Logo * (PNG/JPEG)</label>
                <input type="file" accept="image/png, image/jpeg" onChange={handleFileChange} required />
            </div>

            <button type="submit" disabled={loading}>{loading ? 'Creating...' : 'Create Shop'}</button>
        </form>
    )
}
