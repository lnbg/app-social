<template>
    <!-- Widget: user widget style 1 -->
    <div class="box box-widget widget-user box-instagram">
        <div class="widget-loading" v-if="boxLoading">
            <p><i class="fa fa-cog fa-spin fa-3x fa-fw"></i></p>
        </div>
        <div class="widget-user-control">
            <a href="javascript:void(0)"><i class="fa fa-download" @click="boxAnalyticsInstagramProfile"></i></a>
        </div>
        <!-- Add the bg color to the header using any of the bg-* classes -->
        <div class="widget-user-header" :class="getBackgroundBoxHeader">
            <h3 class="widget-user-username">{{ profile.name }}</h3>
        </div>
        <div class="widget-user-image">
            <img class="img-circle" :src="profile.picture" alt="User Avatar">
        </div>
        <div class="box-footer">
            <div class="row">
                <div class="col-sm-4 border-right">
                    <div class="description-block">
                        <h5 class="description-header">{{ profile.media_counts | currency }}</h5>
                        <span class="description-text">MEDIA</span>
                    </div>
                    <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-4 border-right">
                    <div class="description-block">
                    <h5 class="description-header">{{ profile.followers_count | currency }}</h5>
                    <span class="description-text">FOLLOWERS</span>
                    </div>
                    <!-- /.description-block -->
                </div>
                <!-- /.col -->
                <div class="col-sm-4">
                    <div class="description-block">
                    <h5 class="description-header">{{ profile.follows_count | currency }}</h5>
                    <span class="description-text">FOLLOWS</span>
                    </div>
                    <!-- /.description-block -->
                </div>
            <!-- /.col -->
            </div>
            <!-- /.row -->
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

