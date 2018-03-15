<template>
    <!-- Widget: user widget style 1 -->
    <div class="box box-widget widget-user-2">
        <div class="widget-loading" v-if="boxLoading">
            <p><i class="fa fa-cog fa-spin fa-3x fa-fw"></i></p>
        </div>
        <div class="widget-user-control">
            <a href="javascript:void(0)"><i class="fa fa-download" @click="boxAnalyticsFacebookPage"></i></a>
        </div>
        <!-- Add the bg color to the header using any of the bg-* classes -->
        <div class="widget-user-header min-height-120" :class="getBackgroundBoxHeader">
            <div class="widget-user-image">
                <img class="img-circle" :src="page.account_picture" :alt="page.account_name">
                <h3 class="widget-user-username">{{ page.account_name }}</h3>
                <a style="color: white; display: block;" :href="page.account_link"><h5 class="widget-user-desc" style="word-break: break-all; font-size: 13px;">{{ page.account_link }}</h5></a>
            </div>
        </div>
        <div class="box-footer no-padding">
            <ul class="nav nav-stacked">
                <li><a href="#">Total Posts: <span class="pull-right badge bg-blue">{{ page.total_posts | currency }}</span></a></li>
                <li><a href="#">Total Followers <span class="pull-right badge bg-aqua">{{ page.total_followers | currency }}</span></a></li>
                <li><a href="#">Total Likes <span class="pull-right badge bg-green">{{ page.total_likes | currency }}</span></a></li>
                <li><a href="#">Average Posts/Day <span class="pull-right badge bg-red">{{ page.average_posts_per_day | currency }}</span></a></li>
                <li><a href="#">Average Likes/Post <span class="pull-right badge bg-yellow">{{ page.average_likes_per_post | currency }}</span></a></li>
            </ul>
        </div>
    </div>
</template>
<script>
import { mapState, mapActions } from 'vuex'

export default {
    name: 'G_BoxFacebookPage',
    props: {
        page: {
            id: 0,
            account_id: 0,
            account_name: String.empty,
            account_picture: String.empty,
            account_link: String.empty,
            total_posts: 0,
            total_followers: 0,
            total_likes: 0,
            average_posts_per_day: 0,
            average_likes_per_post: 0,
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
        ...mapActions('facebook', [
           'analyticsFacebookPage' 
        ]),
        boxAnalyticsFacebookPage() {
            this.makeBoxLoading()
            this.analyticsFacebookPage(this.page.id)
                .then(response => {
                    Object.assign(this.page, response.data)
                    this.makeBoxLoaded();
                })
        },
        makeBoxLoading() {
            this.boxLoading = true
        },
        makeBoxLoaded() {
            this.boxLoading = false
        }
    }
}
</script>

