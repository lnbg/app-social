const GET_LIST_FACEBOOK_PAGE_ANALYTICS = (state, data) => {
    state.lstFacebookPageAnalytics = data
};

const GET_FACEBOOK_PAGE_ANALYTICS = (state, data) => {
    Object.assign(state.facebookAnalytics, data.page)
    Object.assign(state.post, data.analytics.bestPost)
    state.facebookLastPosts = data.analytics.facebookLastPosts
    state.facebookGrowthFans = data.analytics.growthFans
    state.facebookEvolutionOfInteractions = data.analytics.evolutionOfInteractions
    state.facebookDistributionOfPostType = data.analytics.facebookDistributionOfPostType
    Object.assign(state.facebookDistributionOfInteraction, data.analytics.facebookDistributionOfInteraction)
}

const RESET_GROWTH_FANS = (state) => {
    state.facebookGrowthFans = []
}

const RESET_EVOLUTION_OF_INTERACTIONS = (state) => {
    state.facebookEvolutionOfInteractions = []
}
  
export default {
    GET_LIST_FACEBOOK_PAGE_ANALYTICS,
    GET_FACEBOOK_PAGE_ANALYTICS,
    RESET_GROWTH_FANS,
    RESET_EVOLUTION_OF_INTERACTIONS
};