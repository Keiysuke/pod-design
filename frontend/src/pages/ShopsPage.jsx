import ShopForm from '../components/ShopForm'
import ShopList from '../components/ShopList'
import React from 'react'

export default function ShopsPage() {
    const [refresh, setRefresh] = React.useState(0)

    return (
        <div className="page shops-page">
            <h2>Shops</h2>
            <ShopForm onShopCreated={() => setRefresh(r => r + 1)} />
            <ShopList refreshTrigger={refresh} />
        </div>
    )
}
