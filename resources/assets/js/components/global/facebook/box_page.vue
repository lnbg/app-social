<template>
    <!-- Widget: user widget style 1 -->
    <div class="box box-widget widget-user-2 box-facebook-page">
        <div class="widget-loading" v-if="boxLoading">
            <p><i class="fa fa-cog fa-spin fa-3x fa-fw"></i></p>
        </div>
        <div class="widget-user-control">
            <router-link :to="overviewLink">
                <i class="fa fa-eye"></i>
            </router-link>&nbsp;&nbsp;
            <a href="javascript:void(0)"><i class="fa fa-download" @click="boxAnalyticsFacebookPage"></i></a>
        </div>
        <!-- Add the bg color to the header using any of the bg-* classes -->
        <div class="widget-user-header min-height-120" :class="getBackgroundBoxHeader">
            <div class="widget-user-image">
                <img class="img-circle" :src="page.account_picture" :alt="page.account_name">
                <h3 class="widget-user-username widget-user-username-f20">{{ page.account_name }}</h3>
                <h5 class="widget-user-username widget-user-username-f14">{{ page.account_username }}</h5>
            </div>
        </div>
        <div class="box-footer no-padding">
            <ul class="nav nav-stacked">
                <li><a href="#">Total Posts: <span class="pull-right badge bg-blue">{{ page.total_posts | currency }}</span></a></li>
                <li><a href="#">Total Page Followers <span class="pull-right badge bg-aqua">{{ page.total_page_followers | currency }}</span></a></li>
                <li><a href="#">Total Fans <span class="pull-right badge bg-aqua">{{ page.total_page_likes | currency }}</span></a></li>
                <li>
                    <a href="#">Total Reactions <span class="pull-right badge bg-green">{{ reactions | currency }}</span></a>
                    <!-- <a href="#">
                        <span><img class="reactions-img" src="/imgs/reactions/love.gif" height="16px" style="margin-left: 0;" />{{ page.total_posts_loves | currency }}</span>
                        <span><img class="reactions-img"  src="/imgs/reactions/like.gif" height="16px" /> {{ page.total_posts_likes | currency }}</span>
                        <span><img class="reactions-img"  src="/imgs/reactions/wow.gif" height="16px" /> {{ page.total_posts_wows | currency }}</span>
                        <span><img class="reactions-img"  src="/imgs/reactions/sad.gif" height="16px" /> {{ page.total_posts_sads | currency }}</span>
                        <span><img class="reactions-img"  src="/imgs/reactions/haha.gif" height="16px" /> {{ page.total_posts_hahas | currency }}</span>
                        <span><img class="reactions-img"  src="/imgs/reactions/angry.gif" height="16px" /> {{ page.total_posts_angries | currency }}</span>
                    </a> -->
                </li>
                <li><a href="#">Total Interactions <span class="pull-right badge bg-green">{{ interactions | currency }}</span></a></li>
                <li><a href="#">Average Posts/Day <span class="pull-right badge bg-yellow">{{ page.average_posts_per_day | currency }}</span></a></li>
                <li><a href="#">Average Reactions/Post <span class="pull-right badge bg-yellow">{{ page.average_reactions_per_post | currency }}</span></a></li>
                <li><a href="#">Average Interactions/Post <span class="pull-right badge bg-yellow">{{ page.average_interactions_per_post | currency }}</span></a></li>
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
            account_username: String.empty,
            account_picture: String.empty,
            account_link: String.empty,
            total_posts: 0,
            total_page_followers: 0,
            total_page_likes: 0,
            total_posts_likes: 0,
            total_posts_loves: 0,
            total_posts_wows: 0,
            total_posts_sads: 0,
            total_posts_hahas: 0,
            total_posts_angries: 0,
            total_posts_shares: 0,
            total_posts_comments: 0,
            total_posts_thankfuls: 0,
            average_posts_per_day: 0,
            average_reactions_per_post: 0,
            average_intereactions_per_post: 0,
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
        reactions() {
            return this.page.total_posts_likes + this.page.total_posts_loves + this.page.total_posts_wows
            + this.page.total_posts_sads + this.page.total_posts_hahas + this.page.total_posts_angries;
        },
        interactions() {
            return this.reactions + this.page.total_posts_shares + this.page.total_posts_comments + this.page.total_posts_thankfuls;
        },
        overviewLink() {
            return '/facebook/overview/' + this.page.account_username
        }
    },
    methods: {
        ...mapActions('facebook', [
           'analyticsFacebookPage' 
        ]),
        boxAnalyticsFacebookPage() {
            this.makeBoxLoading()
            this.analyticsFacebookPage(this.page.id)
                .then(response => {
                    Object.assign(this.page, response);
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
    created() {
        this.$bus.$on('analyticsAll', () => {
            this.boxAnalyticsFacebookPage()
        })
    }
}
</script>

