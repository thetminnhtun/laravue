import Dashboard from './components/Dashboard'
import Users from './components/Users'
import Developer from './components/Developer'
import Profile from './components/Profile'

export const routes = [
    { path: '/dashboard', component: Dashboard },
    { path: '/users', component: Users },
    { path: '/developer', component: Developer },
    { path: '/profile', component: Profile },
  ];