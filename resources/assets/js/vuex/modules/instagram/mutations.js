const GET_LIST_INSTAGRAM_PROFILE_ANALYTICS= (state, data) => {
    state.lstInstagramProfileAnalytics = data
};

const GET_INSTAGRAM_PROFILE_ANALYTICS= (state, data) => {
    Object.assign(state.instagramProfile, data.profile)
    state.instagramGrowthFans = data.analytics.growthFans
    state.instagramTotalMediaPerDay = data.analytics.instagramTotalMediaPerDay
    state.instagramTotalMediaGroupByType = data.analytics.instagramTotalMediaGroupByType
    state.instagramLastMedias = data.analytics.instagramLastMedias
    Object.assign(state.instagramTotalInteraction, data.analytics.instagramTotalInteraction)
    state.instagramEvolutionOfInteractions = data.analytics.instagramEvolutionOfInteractions
    state.instagramDistributionTags = data.analytics.instagramDistributionTags
};

const RESET_GROWTH_FANS = (state) => {
    state.instagramGrowthFans = []
}

const RESET_TOTAL_MEDIA_PER_DAY = (state) => {
    state.instagramTotalMediaPerDay = []
}

const RESET_TOTAL_MEDIA_GROUP_BY_TYPE = (state) => {
    state.instagramTotalMediaGroupByType = []
}
  
export default {
    GET_LIST_INSTAGRAM_PROFILE_ANALYTICS,
    GET_INSTAGRAM_PROFILE_ANALYTICS,
    RESET_GROWTH_FANS,
    RESET_TOTAL_MEDIA_PER_DAY,
    RESET_TOTAL_MEDIA_GROUP_BY_TYPE
    
};