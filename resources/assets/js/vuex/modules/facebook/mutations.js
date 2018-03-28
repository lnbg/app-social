const GET_LIST_FACEBOOK_PAGE_ANALYTICS = (state, data) => {
    state.lstFacebookPageAnalytics = data
};

const GET_FACEBOOK_PAGE_ANALYTICS = (state, data) => {
    Object.assign(state.facebookAnalytics, data.page)
    Object.assign(state.facebookBestPost, data.analytics.bestPost)
    state.facebookGrowthFans = data.analytics.growthFans
    state.facebookEvolutionOfInteractions = data.analytics.evolutionOfInteractions
}
  
export default {
    GET_LIST_FACEBOOK_PAGE_ANALYTICS,
    GET_FACEBOOK_PAGE_ANALYTICS
};