<template>
    <div id="facebook-overview" class="page-overview">
        <div class="page-overview-wrapper">
            <div class="row">
                <box-instagram-profile class="box-overview-instagram" :profile="instagramProfile"></box-instagram-profile>
            </div>
            <div class="instagram-analytic-content row">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-xs-12"> 
                        <section id="instagram-growth-fans-chart">
                            <growth-fans-chart v-if="growthFansLoadSuccess" :boxStyle="'box-success'" :title="'Growth of Followers'" :source="growthFans"></growth-fans-chart>
                        </section>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12"> 
                        <section id="instagram-media-by-type">
                            <media-group-type v-if="totalMediaGroupByTypeLoadSuccess" :boxStyle="'box-success'" :title="'Distribution Of Media Type'" :source="totalMediaGroupByType"></media-group-type>
                        </section>
                    </div>
                </div>
                <div class="row">
                    <section id="instagram-post-per-day">
                        <total-posts-per-day-chart :stacked="false" :title="'Number of Profile Posts'" :boxStyle="'box-success'" v-if="totalMediaPerDayLoadSuccess" :source="totalMediaPerDay"></total-posts-per-day-chart>
                    </section>
                </div>
                <div class="row masonry-grid">
                    <masonry
                        :cols="{default: 3, 800: 2, 400: 1}"
                        :gutter="{default: '15px', 800: '5px'}"
                        >
                        <box-instagram-media v-for="media in instagramLastMedias" :key="media.instagram_created_at" :instagramMedia="media" :instagramProfile="instagramProfile"></box-instagram-media>
                    </masonry>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapState, mapActions, mapGetters } from 'vuex'
import BoxInstagramProfile from '../../global/instagram/box_profile'
import BoxInstagramMedia from '../../global/instagram/instagram_post'
import growthFansChart from '../../global/area-chart'
import totalPostsPerDay from '../../global/bar-chart'
import totalMediaGroupByType from '../../global/pie-chart'


export default {
    name: 'Page_Instagram__Overview',
    components: {
        'box-instagram-profile': BoxInstagramProfile,
        'box-instagram-media': BoxInstagramMedia,
        'growth-fans-chart': growthFansChart,
        'total-posts-per-day-chart': totalPostsPerDay,
        'media-group-type': totalMediaGroupByType
    },
    data() {
        return {    
        }
    },
    computed: {
        ...mapGetters('instagram', [
            'instagramProfile',
            'instagramGrowthFans',
            'instagramTotalMediaPerDay',
            'instagramTotalMediaGroupByType',
            'instagramLastMedias'
        ]),
        growthFans() {
            var data = {
                labels: this.growthFansLabels,
                datasets: [
                    {
                        label               : 'Growth Of Followers',
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
            return this.instagramGrowthFans.map(function(item) {
                return item.date_sync
            })
        },
        growthFansValues() {
            return this.instagramGrowthFans.map(function(item) {
                return item.instagram_followers
            })
        },
        totalMediaPerDay() {
            var data = {
                labels: this.totalMediaPerDayLabels,
                datasets: [
                    {
                        type                : 'bar',
                        label               : 'Number of Profile Posts',
                        backgroundColor     : 'rgb(240, 82, 103)',
                        data: this.totalMediaPerDayValues
                    }
                ]
            }
            return data;
        },
        totalMediaPerDayLabels() {
             return this.instagramTotalMediaPerDay.map(function(item) {
                return item.date
            })
        },
        totalMediaPerDayValues() {
             return this.instagramTotalMediaPerDay.map(function(item) {
                return item.value
            })
        },
        totalMediaGroupByType() {
            var data = {
                labels: this.totalMediaGroupByTypeLabels,
                datasets: [
                    {
                        type: 'pie',
                        backgroundColor: [
                            "rgb(0, 166, 90)",
                            "rgb(243, 156, 18)",
                            "rgb(221, 75, 57)"
                        ],
                        borderColor: [
                            "rgb(255, 255, 255)",
                            "rgb(255, 255, 255)",
                            "rgb(255, 255, 255)",
                        ],
                        data: this.totalMediaGroupByTypeValues
                    }
                ]
            }
            return data;
        },
        totalMediaGroupByTypeLabels() {
            return this.instagramTotalMediaGroupByType.map(function(item) {
                return item.media_type
            })
        },
        totalMediaGroupByTypeValues() {
            return this.instagramTotalMediaGroupByType.map(function(item) {
                return item.value
            })
        },
        growthFansLoadSuccess() {
            return this.growthFansLabels.length > 0
        },
        totalMediaGroupByTypeLoadSuccess() {
            return this.totalMediaGroupByTypeLabels.length > 0
        },
        totalMediaPerDayLoadSuccess() {
            return this.totalMediaPerDayLabels.length > 0
        }
    },
    methods: {
        ...mapActions('instagram', [
            'resetGrowthFans',
            'resetTotalMediaPerDay',
            'resetTotalMediaGroupByType'
        ]),
    },
    beforeCreate() {
        this.$store.dispatch('instagram/getInstagramProfileAnalytics', this.$route.params.username)
    },
    created() {

    },
    beforeDestroy() {
        this.resetGrowthFans()
        this.resetTotalMediaPerDay()
    }
}
</script>
