<template>
    <div id="dashboard">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Facebook Ranking</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <section id="table-ranking">
                    <!-- HTML -->
                    <best-kols-growth-chart></best-kols-growth-chart>
                </section>
            </div>
        </div>
    </div>
</template>
<script>
import { mapState, mapActions, mapGetters } from 'vuex'
import getKolsGrowthLineChart from '../../global/my-bar-chart'

export default {
    name: 'Page__Index',
    components: {
        'best-kols-growth-chart': getKolsGrowthLineChart
    },
    data() {
        return {
           
        }
    },
    computed: {
        ...mapGetters('facebook', [
            'facebookTopFanRanking'
        ]),
    },
    methods: {
        ...mapActions('facebook', [
        ]),
    },
    beforeCreate() {
        this.$store.dispatch('facebook/getFacebookPageRanking')
    },
    created() {
        
    }
}
</script>