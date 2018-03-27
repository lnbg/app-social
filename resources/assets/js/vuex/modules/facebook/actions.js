import { post, get, queryString } from '../../http/http';
import { endPoint } from './endpoint';

const getListFacebookPageAnalytics = ({commit, state}) => {
    get(endPoint.GET.LIST_FACEBOOK_PAGE_ANALYTICS)
        .then(response => {
            commit('GET_LIST_FACEBOOK_PAGE_ANALYTICS', response.data);
        })
}

const createNewFacebookPage = (event, facebook_link) => {
    return new Promise((resolve, reject) => {
        post(endPoint.POST.CREATE_NEW_FACEBOOK_PAGE, {page_link: facebook_link})
        .then(response => {
            resolve(response.data);
        })
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
    createNewFacebookPage,
    analyticsFacebookPage
};