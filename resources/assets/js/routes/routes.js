import DashboardIndex from '../components/page/dashboard/index'
import FacebookPage from '../components/page/facebook/page'
import FacebookOverview from '../components/page/facebook/overview'
import Instagram from '../components/page/instagram/index'
import InstagramOverview from '../components/page/instagram/overview'
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
    },
    {
        path: '/instagram/overview/:username',
        name: 'instagram_overview',
        component: InstagramOverview,
    },
    { path: '*', component: DashboardIndex }
]

export default routes