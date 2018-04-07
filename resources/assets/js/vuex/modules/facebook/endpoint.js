import { API_DOMAIN } from '../../http/http'

export const endPoint = {
    GET: {
        LIST_FACEBOOK_PAGE_ANALYTICS: `${API_DOMAIN}/facebook/get-list-facebook-page-analytics`,
        FACEBOOK_PAGE_ANALYTICS: `${API_DOMAIN}/facebook/get-facebook-page-analytics`,
        FACEBOOK_PAGE_RANKING: `${API_DOMAIN}/facebook/get-page-ranking`
    },
    POST: {
        CREATE_NEW_FACEBOOK_PAGE: `${API_DOMAIN}/facebook/create-new-facebook-page`,
        ANALYTICS_FACEBOOK_PAGE: `${API_DOMAIN}/facebook/analytics-facebook-page`
    }
}