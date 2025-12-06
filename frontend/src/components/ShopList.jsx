import { useEffect, useState } from 'react'
import axios from 'axios'
import './ShopList.css'

export default function ShopList({ refreshTrigger }) {
    const [shops, setShops] = useState([])
    const [loading, setLoading] = useState(true)
    const [error, setError] = useState(null)

    const fetchShops = async () => {
        try {
            setLoading(true)
            const res = await axios.get('http://127.0.0.1:8000/api/shops')
            setShops(res.data.data || [])
            setError(null)
        } catch (err) {
            setError('Failed to fetch shops')
            console.error(err)
        } finally {
            setLoading(false)
        }
    }

    useEffect(() => { fetchShops() }, [refreshTrigger])

    if (loading) return <div>Loading shops...</div>
    if (error) return <div className="error">{error}</div>

    return (
        <div className="shop-list">
            <h3>Shops ({shops.length})</h3>
            {shops.length === 0 ? (
                <p>No shops yet</p>
            ) : (
                <div className="shops-grid">
                    {shops.map(s => (
                        <div key={s.id} className="shop-card">
                            {s.logo ? (
                                <img src={`http://127.0.0.1:8000/shops/${s.id}.${s.logo}`} alt={s.name} className="shop-logo" />
                            ) : (
                                <div className="shop-logo placeholder">No logo</div>
                            )}
                            <h4>{s.name}</h4>
                            <a href={s.url} target="_blank" rel="noreferrer" className="shop-link">Visit</a>
                        </div>
                    ))}
                </div>
            )}
        </div>
    )
}
