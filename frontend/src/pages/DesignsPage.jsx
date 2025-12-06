import React from 'react'
import DesignForm from '../components/DesignForm'
import DesignList from '../components/DesignList'

export default function DesignsPage() {
    const [refresh, setRefresh] = React.useState(0)

    return (
        <div className="page designs-page">
            <h2>Designs</h2>
            <DesignForm onDesignCreated={() => setRefresh(r => r + 1)} />
            <DesignList refreshTrigger={refresh} />
        </div>
    )
}
