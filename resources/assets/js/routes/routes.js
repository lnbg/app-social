import DashboardIndexV1 from '../components/page/dashboard/index-v1'
import FacebookPage from '../components/page/facebook/page'
import FacebookOverview from '../components/page/facebook/overview'
import Instagram from '../components/page/instagram/index'
const routes = [
    {
        path: '/facebook/page',
        component: FacebookPage,
    },
    {
        path: '/facebook/overview/:username',
        component: FacebookOverview
    },
        {
        path: '/instagram',
        component: Instagram,
    }, { path: '*', component: DashboardIndexV1 }
]

export default routes