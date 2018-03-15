import { post, get, queryString } from '../../http/http';
import { endPoint } from './endpoint';

const getListFacebookPageAnalytics = ({commit, state}) => {
    get(endPoint.GET.LIST_FACEBOOK_PAGE_ANALYTICS)
        .then(response => {
            commit('GET_LIST_FACEBOOK_PAGE_ANALYTICS', response.data);
        })
}
const analyticsFacebookPage = (event, id) => {
    return new Promise((resolve, reject) => {
        post(endPoint.POST.ANALYTICS_FACEBOOK_PAGE, {id: id})
        .then(response => {
            resolve(response.data);
        })
    })
}

export default {
    getListFacebookPageAnalytics,
    analyticsFacebookPage
};