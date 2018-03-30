const GET_LIST_INSTAGRAM_PROFILE_ANALYTICS= (state, data) => {
    state.lstInstagramProfileAnalytics = data
};

const GET_INSTAGRAM_PROFILE_ANALYTICS= (state, data) => {
    Object.assign(state.instagramProfile, data.profile)
    state.instagramGrowthFans = data.analytics.growthFans
    state.instagramTotalMediaPerDay = data.analytics.instagramTotalMediaPerDay
};

const RESET_GROWTH_FANS = (state) => {
    state.instagramGrowthFans = []
}

const RESET_TOTAL_MEDIA_PER_DAY = (state) => {
    state.instagramTotalMediaPerDay = []
}
  
export default {
    GET_LIST_INSTAGRAM_PROFILE_ANALYTICS,
    GET_INSTAGRAM_PROFILE_ANALYTICS,
    RESET_GROWTH_FANS,
    RESET_TOTAL_MEDIA_PER_DAY
    
};