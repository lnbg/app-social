<template>
    <div id="facebook-overview" class="page-overview">
        <div class="page-overview-wrapper">
            <div class="row">
                <box-instagram-profile class="box-overview-instagram" :profile="instagramProfile"></box-instagram-profile>
            </div>
            <div class="instagram-analytic-content row">
                <div class="col-md-6 col-sm-6 col-xs-12"> 
                    <section id="facebook-growth-fans-chart">
                        <growth-fans-chart v-if="chartLoadSuccess" :boxStyle="'box-success'" :title="'Growth of Followers'" :source="growthFans"></growth-fans-chart>
                    </section>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-12"> 
                    <section id="facebook-growth-fans-chart">
                        <total-posts-per-day-chart :stacked="false" :title="'Number of Profile Posts'" :boxStyle="'box-success'" v-if="chartLoadSuccess" :source="totalMediaPerDay"></total-posts-per-day-chart>
                    </section>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapState, mapActions, mapGetters } from 'vuex'
import BoxInstagramProfile from '../../global/instagram/box_profile'
import growthFansChart from '../../global/area-chart'
import totalPostsPerDay from '../../global/bar-chart'


export default {
    name: 'Page_Instagram__Overview',
    components: {
        'box-instagram-profile': BoxInstagramProfile,
        'growth-fans-chart': growthFansChart,
        'total-posts-per-day-chart': totalPostsPerDay
    },
    data() {
        return {    
        }
    },
    computed: {
        ...mapGetters('instagram', [
            'instagramProfile',
            'instagramGrowthFans',
            'instagramTotalMediaPerDay'
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
        chartLoadSuccess() {
            return this.growthFans.labels.length > 0
        }
    },
    methods: {
        ...mapActions('instagram', [
            'resetGrowthFans',
            'resetTotalMediaPerDay'
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
