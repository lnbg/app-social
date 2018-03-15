<template>
    <div id="fanpage-wrapper">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Add new Page</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <form action="/facebook/add-new-page" method="POST">
                    <input type="text" class="form-control" style="width: 400px; display: inline-block;" name="page_link" id="page_link" placeholder="Link to page" />
                    <input type="submit" style="display: inline-block; margin-top: -5px; padding-left: 10px; padding-right: 10px;" class="btn btn-primary" value="Add">
                </form>
            </div>
        </div>
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Data Analytics</h3>
                <div class="box-tools">
                    <a class="btn btn-box-tool" title="Fetching"><i class="fa fa-download"></i> Analytics</a>
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
        return {}
    },
    computed: {
        ...mapState('facebook', [
            'lstFacebookPageAnalytics'
        ]),

    },
    methods: {
        ...mapActions('facebook', [])
    },
    beforeCreate() {
        this.$store.dispatch('facebook/getListFacebookPageAnalytics')
    },
    created() {

    }
}
</script>
