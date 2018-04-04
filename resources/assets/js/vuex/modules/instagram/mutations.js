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

const RESET_INSTAGRAM_LAST_MEDIA = (state) => {
    state.instagramLastMedias = []
}

const RESET_INSTAGRAM_EVOLUTION_OF_INTERACTION = (state) => {
    state.instagramEvolutionOfInteractions = []
}

const RESET_INSTAGRAM_TOTAL_INTERACTION = (state) => {
    state.instagramTotalInteraction = {}
}

const RESET_INSTAGRAM_DISTRIBUTION_TAGS = (state) => {
    state.instagramDistributionTags = []
}

export default {
    GET_LIST_INSTAGRAM_PROFILE_ANALYTICS,
    GET_INSTAGRAM_PROFILE_ANALYTICS,
    RESET_GROWTH_FANS,
    RESET_TOTAL_MEDIA_PER_DAY,
    RESET_TOTAL_MEDIA_GROUP_BY_TYPE,
    RESET_INSTAGRAM_LAST_MEDIA,
    RESET_INSTAGRAM_EVOLUTION_OF_INTERACTION,
    RESET_INSTAGRAM_TOTAL_INTERACTION,
    RESET_INSTAGRAM_DISTRIBUTION_TAGS
};