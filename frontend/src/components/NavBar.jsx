import { Link, NavLink } from 'react-router-dom'
import './NavBar.css'

export default function NavBar() {
    return (
        <nav className="nav">
            <div className="nav-brand"><Link to="/">POD Designs</Link></div>
            <ul className="nav-links">
                <li><NavLink to="/designs">Designs</NavLink></li>
                <li><NavLink to="/shops">Shops</NavLink></li>
                <li><NavLink to="/associations">Associations</NavLink></li>
            </ul>
        </nav>
    )
}
