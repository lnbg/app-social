import { API_DOMAIN } from '../../http/http'

export const endPoint = {
    GET: {
        LIST_FACEBOOK_PAGE_ANALYTICS: `${API_DOMAIN}/facebook/get-list-facebook-page-analytics`
    },
    POST: {
        ANALYTICS_FACEBOOK_PAGE: `${API_DOMAIN}/facebook/analytics_facebook_page`
    }
}