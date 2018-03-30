<template>
    <div id="facebook-overview" class="page-overview">
        <div class="page-overview-wrapper">
            <div class="row">
                <div class="overview-panel-left col-md-4">
                    <div class="box box-widget widget-user overview-box-widget">
                        <!-- Add the bg color to the header using any of the bg-* classes -->
                        <div class="widget-user-header bg-black min-height-120 bg-image" :style="styleForCoverPicture">
                            <div class="widget-user-image">
                                <img class="img-circle" :src="facebookAnalytics.account_picture" :alt="facebookAnalytics.account_name">
                            </div>
                        </div>
                        <div class="box-footer">
                            <ul class="nav nav-stacked">
                                <li style="text-align: center"><a :href="facebookAnalytics.account_link"><span class="badge bg-yellow">{{ facebookAnalytics.account_link }}</span></a></li>
                                <li><a href="#">Total Posts: <span class="pull-right badge bg-blue">{{ facebookAnalytics.total_posts | currency }}</span></a></li>
                                <li><a href="#">Total Page Followers <span class="pull-right badge bg-aqua">{{ facebookAnalytics.total_page_followers | currency }}</span></a></li>
                                <li><a href="#">Total Fans <span class="pull-right badge bg-aqua">{{ facebookAnalytics.total_page_likes | currency }}</span></a></li>
                                <li><a href="#">Total Reactions <span class="pull-right badge bg-green">{{ reactions | currency }}</span></a></li>
                                <li><a href="#">Total Interactions <span class="pull-right badge bg-green">{{ interactions | currency }}</span></a></li>
                                <li><a href="#">Average Posts/Day <span class="pull-right badge bg-yellow">{{ facebookAnalytics.average_posts_per_day | currency }}</span></a></li>
                                <li><a href="#">Average Reactions/Post <span class="pull-right badge bg-yellow">{{ facebookAnalytics.average_reactions_per_post | currency }}</span></a></li>
                                <li><a href="#">Average Interactions/Post <span class="pull-right badge bg-yellow">{{ facebookAnalytics.average_interactions_per_post | currency }}</span></a></li>
                            </ul>
                        </div>
                    </div>
                    <section id="facebook-most-intereactions-post">
                        <facebook-post v-if="chartLoadSuccess" :facebookAnalytics="facebookAnalytics" :post="facebookBestPost"></facebook-post>
                    </section>
                </div>
                <div class="overview-panel-right col-md-8">
                    <section id="facebook-posts-analytics">
                        <div class="row">
                            <div class="analytics-reactions col-md-2 col-sm-4">
                                <reactions-box id="likes" :color="'bg-aqua'" :title="'Like'" :icon="'/imgs/reactions/like.gif'" :value="facebookAnalytics.total_posts_likes"></reactions-box>
                            </div>
                            <div class="analytics-reactions col-md-2 col-sm-4">
                                <reactions-box id="loves" :color="'bg-red'" :title="'Love'" :icon="'/imgs/reactions/love.gif'" :value="facebookAnalytics.total_posts_loves"></reactions-box>
                            </div>
                            <div class="analytics-reactions col-md-2 col-sm-4">
                                <reactions-box id="haha" :color="'bg-blue'" :title="'Haha'" :icon="'/imgs/reactions/haha.gif'" :value="facebookAnalytics.total_posts_hahas"></reactions-box>
                            </div>
                            <div class="analytics-reactions col-md-2 col-sm-4">
                                <reactions-box id="wow" :color="'bg-yellow'" :title="'Wow'" :icon="'/imgs/reactions/wow.gif'" :value="facebookAnalytics.total_posts_wows"></reactions-box>
                            </div>
                            <div class="analytics-reactions col-md-2 col-sm-4">
                                <reactions-box id="sad" :color="'bg-green'" :title="'Sad'" :icon="'/imgs/reactions/sad.gif'" :value="facebookAnalytics.total_posts_sads"></reactions-box>
                            </div>
                            <div class="analytics-reactions col-md-2 col-sm-4">
                                <reactions-box id="angry" :color="'bg-red'" :title="'Angry'" :icon="'/imgs/reactions/angry.gif'" :value="facebookAnalytics.total_posts_angries"></reactions-box>
                            </div>
                        </div>
                    </section>
                    <section id="facebook-growth-fans-chart">
                        <div class="row">
                            <growth-fans-chart v-if="chartLoadSuccess" :boxStyle="'box-info'" :title="'Growth of Fans'" :source="growthFans"></growth-fans-chart>
                        </div>
                    </section>
                    <section id="facebook-evolution-of-interactions-chart">
                        <div class="row">
                            <evolution-of-interactions-chart :stacked="true" :title="'Evolution of Interactions'" :boxStyle="'box-info'" v-if="chartLoadSuccess" :source="evolutionOfInteractions"></evolution-of-interactions-chart>
                        </div>
                    </section>
                    <section id="facebook-posts-timeline">
                        <div class="row">
                            <posts-timeline v-if="chartLoadSuccess" :facebookAnalytics="facebookAnalytics" :source="facebookLastPosts"></posts-timeline>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapState, mapActions, mapGetters } from 'vuex'
import reactionsBox from '../../global/facebook/reactions_box'
import growthFansChart from '../../global/area-chart'
import evolutionOfInteractionsChart from '../../global/bar-chart'
import facebookPost from '../../global/facebook/facebook_post'
import facebookPostsTimeLine from '../../global/facebook/posts_timeline'

export default {
    name: 'Page_Facebook__Overview',
    components: {
        'reactions-box': reactionsBox,
        'growth-fans-chart': growthFansChart,
        'evolution-of-interactions-chart': evolutionOfInteractionsChart,
        'facebook-post': facebookPost,
        'posts-timeline': facebookPostsTimeLine
    },
    data() {
        return {    
        }
    },
    computed: {
        ...mapGetters('facebook', [
            'facebookAnalytics',
            'facebookGrowthFans',
            'facebookEvolutionOfInteractions',
            'facebookLastPosts',
            'facebookBestPost'
        ]),
        styleForCoverPicture() {
            return `background: url('${this.facebookAnalytics.account_picture_cover}'); background-size: cover;`;
        },
        reactions() {
            return this.facebookAnalytics.total_posts_likes + this.facebookAnalytics.total_posts_loves + this.facebookAnalytics.total_posts_wows
            + this.facebookAnalytics.total_posts_sads + this.facebookAnalytics.total_posts_hahas + this.facebookAnalytics.total_posts_angries;
        },
        interactions() {
            return this.reactions + this.facebookAnalytics.total_posts_shares + this.facebookAnalytics.total_posts_comments + this.facebookAnalytics.total_posts_thankfuls;
        },
        getBackgroundBoxHeader() {
            let bgColors =  ['bg-blue', 'bg-aqua', 'bg-green', 'bg-yellow', 'bg-red']
            return bgColors[Math.floor(Math.random() * bgColors.length)];
        },
        growthFans() {
            var data = {
                labels: this.growthFansLabels,
                datasets: [
                    {
                        label               : 'Growth Of Fans',
                        backgroundColor     : 'rgb(3, 192, 239, 0.75)',
                        pointColor          : 'rgb(243, 156, 18)',
                        pointStrokeColor    : 'rgb(243, 156, 18)',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(220,220,220,1)',
                        data: this.growthFansValues
                    }
                ]
            }
            return data;
        },
        growthFansLabels() {
            return this.facebookGrowthFans.map(function(item) {
                return item.date_sync
            })
        },
        growthFansValues() {
            return this.facebookGrowthFans.map(function(item) {
                return item.facebook_fans
            })
        },
        evolutionOfInteractions() {
            var data = {
                labels: this.evolutionOfInteractionsLabels,
                datasets: [
                    {
                        type                : 'bar',
                        label               : 'Reactions',
                        backgroundColor     : 'rgb(240, 82, 103)',
                        data: this.evolutionOfInteractionsReactionsValues
                    },
                    {
                        type                : 'bar',
                        label               : 'Comments',
                        backgroundColor     : 'rgb(0, 166, 90)',
                        data: this.evolutionOfInteractionsCommentsValues
                    },
                    {
                        type                : 'bar',
                        label               : 'Shares',
                        backgroundColor     : 'rgb(243, 156, 18)',
                        data: this.evolutionOfInteractionsSharesValues
                    }
                ]
            }
            return data;
        },
        evolutionOfInteractionsLabels() {
             return this.facebookEvolutionOfInteractions.map(function(item) {
                return item.date
            })
        },
        evolutionOfInteractionsReactionsValues() {
             return this.facebookEvolutionOfInteractions.map(function(item) {
                return item.reactions
            })
        },
        evolutionOfInteractionsCommentsValues() {
             return this.facebookEvolutionOfInteractions.map(function(item) {
                return item.comments
            })
        },
        evolutionOfInteractionsSharesValues() {
             return this.facebookEvolutionOfInteractions.map(function(item) {
                return item.shares
            })
        },
        chartLoadSuccess() {
            return this.growthFans.labels.length > 0
        }
        
    },
    methods: {
        ...mapActions('facebook', [
            'resetGrowthFans',
            'resetEvolutionOfInteractions'
        ]),
    },
    beforeCreate() {
        this.$store.dispatch('facebook/getFacebookPageAnalytics', this.$route.params.username);
    },
    created() {
    },
    beforeDestroy() {
        this.resetGrowthFans()
        this.resetEvolutionOfInteractions()
    }
}
</script>
