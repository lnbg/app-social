<template>
    <div id="fanpage-wrapper">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Add new facebook page</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <input type="text" v-model="facebookPageLink" class="form-control" style="width: 400px; display: inline-block;" placeholder="Link to page" />
                <input type="button" @click="createNewPage" style="display: inline-block; margin-top: -5px; padding-left: 10px; padding-right: 10px;" class="btn btn-primary" value="Add">
            </div>
        </div>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">FACBOOK FANPAGE <i style="font-size: 10px">(analytics facebook for last 3 months)</i></h3>
                <div class="box-tools">
                    <a class="btn btn-box-tool" @click="analyticsAll" title="Fetching"><i class="fa fa-download"></i> Analytics</a>
                    <a class="btn btn-box-tool" @click="orderByFollowers" title="Order by followers"><i class="fa fa-sort"></i> Order by Followers</a>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <div class="col-md-4" v-for="facebookAnalytics in lstFacebookPageAnalytics" :key="facebookAnalytics.account_id">
                    <box-facebook-page :page="facebookAnalytics"></box-facebook-page>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapState, mapActions } from 'vuex'
import BoxFacebookPage from '../../global/facebook/box_page'

export default {
    name: 'Page_Facebook__Fanpage',
    components: {
        'box-facebook-page' : BoxFacebookPage
    },
    data() {
        return {
            facebookPageLink: String.empty,
            orderBy: 1
        }
    },
    computed: {
        ...mapState('facebook', [
            'lstFacebookPageAnalytics'
        ]),

    },
    methods: {
        ...mapActions('facebook', [
            'createNewFacebookPage',
            'getListFacebookPageAnalytics'
        ]),
        createNewPage() {
            this.createNewFacebookPage(this.facebookPageLink)
                .then(response => {
                    this.getListFacebookPageAnalytics()
                })
        },
        analyticsAll() {
            this.$bus.$emit('analyticsAll')
        },
        orderByFollowers() {
            var vue = this;
            this.orderBy = this.orderBy * -1
            this.lstFacebookPageAnalytics.sort(function(obj1, obj2) {
                return vue.orderBy * (obj2.total_page_followers - obj1.total_page_followers)
            });
        }
    },
    beforeCreate() {
        this.$store.dispatch('facebook/getListFacebookPageAnalytics')
    },
    created() {
        
    }
}
</script>
