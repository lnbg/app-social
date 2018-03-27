<template>
    <!-- Widget: user widget style 1 -->
    <div class="box box-widget widget-user-2 box-instagram">
        <div class="widget-loading" v-if="boxLoading">
            <p><i class="fa fa-cog fa-spin fa-3x fa-fw"></i></p>
        </div>
        <div class="widget-user-control">
            <a href="javascript:void(0)"><i class="fa fa-download" @click="boxAnalyticsInstagramProfile"></i></a>
        </div>
        <!-- Add the bg color to the header using any of the bg-* classes -->
        <div class="widget-user-header min-height-150" :class="getBackgroundBoxHeader">
            <div class="widget-user-image">
                <img class="img-circle" :src="profile.picture" :alt="profile.name">
                <h3 class="widget-user-username">{{ profile.name }}</h3>
                <a style="color: white; display: block;" :href="profile.link"><h5 class="widget-user-desc" style="word-break: break-all; font-size: 11px;">{{ profile.link }}</h5></a>
                <a style="color: white; display: block;" :href="profile.website"><h5 class="widget-user-desc" style="word-break: break-all; font-size: 11px;">{{ profile.website }}</h5></a>
            </div>
        </div>
        <div class="box-footer no-padding">
            <ul class="nav nav-stacked">
                <li><a href="#">Total Media: <span class="pull-right badge bg-blue">{{ profile.media_counts | currency }}</span></a></li>
                <li><a href="#">Total Followers <span class="pull-right badge bg-aqua">{{ profile.followers_count | currency }}</span></a></li>
            </ul>
        </div>
    </div>
</template>
<script>
import { mapState, mapActions } from 'vuex'

export default {
    name: 'G_BoxInstagramProfile',
    props: {
        profile: {
            id: 0,
            instagram_id: 0,
            name: String.empty,
            user_name: String.empty,
            link: String.empty,
            picture: String.empty,
            website: 0,
            biography: String.empty,
            followers_count: 0,
            follows_count: 0,
            media_counts: 0,
        }
    },
    data() {
        return {
            boxLoading: false,
        }
    },
    computed: {
        getBackgroundBoxHeader() {
            let bgColors =  ['bg-blue', 'bg-aqua', 'bg-green', 'bg-yellow', 'bg-red']
            return bgColors[Math.floor(Math.random() * bgColors.length)];
        },
    },
    methods: {
        ...mapActions('instagram', [
            'analyticsInstagramProfile'
        ]),
        boxAnalyticsInstagramProfile() {
            this.makeBoxLoading()
            this.analyticsInstagramProfile(this.profile.id)
                .then(response => {
                    Object.assign(this.profile, response);
                    this.makeBoxLoaded();
                })
        },
        makeBoxLoading() {
            this.boxLoading = true
        },
        makeBoxLoaded() {
            this.boxLoading = false
        }
    },
    created() {}
}
</script>

