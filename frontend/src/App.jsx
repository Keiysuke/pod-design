import React, { useState } from 'react'
import { Routes, Route, Navigate } from 'react-router-dom'
import NavBar from './components/NavBar'
import DesignsPage from './pages/DesignsPage'
import ShopsPage from './pages/ShopsPage'
import AssociationsPage from './pages/AssociationsPage'
import DesignEditModal from './components/DesignEditModal'
import './App.css'

export default function App() {
    const [selectedDesign, setSelectedDesign] = useState(null)

    return (
        <div className="App">
            <header className="app-header">
                <h1>🎨 POD Design Manager</h1>
                <p>Create and manage your Print-on-Demand designs</p>
            </header>

            <NavBar />

            <main className="app-main">
                <Routes>
                    <Route path="/" element={<Navigate to="/designs" replace />} />
                    <Route path="/designs" element={<DesignsPage />} />
                    <Route path="/shops" element={<ShopsPage />} />
                    <Route path="/associations" element={<AssociationsPage />} />
                </Routes>
            </main>

            {selectedDesign && (
                <DesignEditModal
                    design={selectedDesign}
                    onClose={() => setSelectedDesign(null)}
                    onUpdated={() => setSelectedDesign(null)}
                    onDeleted={() => setSelectedDesign(null)}
                />
            )}
        </div>
    )
}
