import { post, get, queryString } from '../../http/http';
import { endPoint } from './endpoint';

const getListInstagramProfileAnalytics = ({commit, state}) => {
    get(endPoint.GET.GET_LIST_INSTAGRAM_PROFILE_ANALYTICS)
        .then(response => {
            commit('GET_LIST_INSTAGRAM_PROFILE_ANALYTICS', response.data);
        })
}

const analyticsInstagramProfile = (event, instagram_analytics_id) => {
    return new Promise((resolve, reject) => {
        post(endPoint.POST.ANALYTICS_INSTAGRAM_PROFILE, {id: instagram_analytics_id})
            .then(response => {
                resolve(response.data);
            })
    })
}

const createNewInstagramProfile = (event, instagram_link) => {
    return new Promise((resolve, reject) => {
        post(endPoint.POST.CREATE_NEW_INSTAGRAM_PROFILE, {instagram_link})
        .then(response => {
            resolve(response.data);
        })
    })
}

export default {
    getListInstagramProfileAnalytics,
    createNewInstagramProfile,
    analyticsInstagramProfile
};