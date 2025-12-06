import { useEffect, useState } from 'react'
import axios from 'axios'

export default function DesignShopForm({ onAssociated }) {
    const [designs, setDesigns] = useState([])
    const [shops, setShops] = useState([])
    const [form, setForm] = useState({ design_id: '', shop_id: '', publishedAt: '', lastUpdateAt: '' })
    const [loading, setLoading] = useState(false)
    const [error, setError] = useState(null)

    useEffect(() => {
        const fetch = async () => {
            try {
                const [dRes, sRes] = await Promise.all([
                    axios.get('http://127.0.0.1:8000/api/designs'),
                    axios.get('http://127.0.0.1:8000/api/shops'),
                ])
                setDesigns(dRes.data.data || [])
                setShops(sRes.data.data || [])
            } catch (err) {
                console.error(err)
            }
        }
        fetch()
    }, [])

    const handleChange = (e) => setForm(prev => ({ ...prev, [e.target.name]: e.target.value }))

    const toIso = (val) => {
        if (!val) return ''
        // input type datetime-local gives 'YYYY-MM-DDTHH:mm' — convert to ISO
        const d = new Date(val)
        return isNaN(d.getTime()) ? '' : d.toISOString()
    }

    const handleSubmit = async (e) => {
        e.preventDefault()
        setLoading(true)
        setError(null)
        try {
            const payload = new URLSearchParams()
            payload.append('design_id', form.design_id)
            payload.append('shop_id', form.shop_id)
            if (form.publishedAt) payload.append('publishedAt', toIso(form.publishedAt))
            if (form.lastUpdateAt) payload.append('lastUpdateAt', toIso(form.lastUpdateAt))

            const res = await axios.post('http://127.0.0.1:8000/api/design-shops', payload)
            onAssociated?.(res.data.data)
            setForm({ design_id: '', shop_id: '', publishedAt: '', lastUpdateAt: '' })
        } catch (err) {
            setError(err.response?.data?.message || 'Association failed')
            console.error(err)
        } finally {
            setLoading(false)
        }
    }

    return (
        <form className="design-shop-form" onSubmit={handleSubmit}>
            <h2>Associate Design → Shop</h2>
            {error && <div className="alert alert-error">{error}</div>}

            <div className="form-group">
                <label>Design</label>
                <select name="design_id" value={form.design_id} onChange={handleChange} required>
                    <option value="">— select design —</option>
                    {designs.map(d => <option key={d.id} value={d.id}>{d.title}</option>)}
                </select>
            </div>

            <div className="form-group">
                <label>Shop</label>
                <select name="shop_id" value={form.shop_id} onChange={handleChange} required>
                    <option value="">— select shop —</option>
                    {shops.map(s => <option key={s.id} value={s.id}>{s.name}</option>)}
                </select>
            </div>

            <div className="form-group">
                <label>Published at</label>
                <input type="datetime-local" name="publishedAt" value={form.publishedAt} onChange={handleChange} />
            </div>

            <div className="form-group">
                <label>Last update</label>
                <input type="datetime-local" name="lastUpdateAt" value={form.lastUpdateAt} onChange={handleChange} />
            </div>

            <button type="submit" disabled={loading}>{loading ? 'Saving...' : 'Associate'}</button>
        </form>
    )
}
