import { API_DOMAIN } from '../../http/http'

export const endPoint = {
    GET: {
        GET_LIST_INSTAGRAM_PROFILE_ANALYTICS: `${API_DOMAIN}/instagram/get-list-instagram-profile-analytics`
    },
    POST: {
        CREATE_NEW_INSTAGRAM_PROFILE: `${API_DOMAIN}/instagram/create-new-instagram-profile`,
        ANALYTICS_INSTAGRAM_PROFILE: `${API_DOMAIN}/instagram/analytics-instagram-profile`
    }
}