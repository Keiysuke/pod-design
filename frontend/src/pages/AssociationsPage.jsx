import DesignShopForm from '../components/DesignShopForm'
import React from 'react'

export default function AssociationsPage() {
    const [refresh, setRefresh] = React.useState(0)

    return (
        <div className="page associations-page">
            <h2>Associations</h2>
            <DesignShopForm onAssociated={() => setRefresh(r => r + 1)} />
            {/* Could add a list of associations here later */}
        </div>
    )
}
