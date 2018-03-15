import DashboardIndexV1 from '../components/page/dashboard/index-v1'
import FacebookPage from '../components/page/facebook/page'
const routes = [{
        path: '/facebook/page',
        component: FacebookPage,
    }, { path: '*', component: DashboardIndexV1 }
]

export default routes