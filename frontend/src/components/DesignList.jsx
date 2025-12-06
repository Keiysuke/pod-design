import { useEffect, useState } from 'react'
import axios from 'axios'
import './DesignList.css'

export default function DesignList({ refreshTrigger, onSelect }) {
    const [designs, setDesigns] = useState([])
    const [loading, setLoading] = useState(true)
    const [error, setError] = useState(null)

    const fetchDesigns = async () => {
        try {
            setLoading(true)
            const response = await axios.get('http://127.0.0.1:8000/api/designs')
            setDesigns(response.data.data || [])
            setError(null)
        } catch (err) {
            setError('Failed to fetch designs')
            console.error('Error:', err)
        } finally {
            setLoading(false)
        }
    }

    useEffect(() => {
        fetchDesigns()
    }, [refreshTrigger])

    if (loading) return <div className="loading">Loading designs...</div>
    if (error) return <div className="error">{error}</div>

    return (
        <div className="design-list">
            <h2>Your Designs ({designs.length})</h2>

            {designs.length === 0 ? (
                <p className="no-designs">No designs yet. Create your first design above!</p>
            ) : (
                <div className="designs-grid">
                    {designs.map(design => (
                        <div key={design.id} className="design-card" onClick={() => onSelect?.(design)} role="button" tabIndex={0}>
                            {design.picture ? (
                                <div className="design-preview">
                                    <img src={`http://127.0.0.1:8000/designs/${design.id}.${design.picture}`} alt={design.title} />
                                </div>
                            ) : (
                                <div className="design-preview placeholder">
                                    <div className="placeholder-box">No image</div>
                                </div>
                            )}

                            <div className="design-title">{design.title}</div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    )
}
