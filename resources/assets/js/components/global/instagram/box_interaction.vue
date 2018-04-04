<template>
    <!-- small box -->
    <div class="small-box" :class="bg">
        <div class="inner">
            <h3>{{ value }}</h3>
            <p>{{ title }}</p>
        </div>
        <div class="icon">
            <i class="fa" :class="icon"></i>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        value: String,
        title: String,
        icon: String,
        bg: String
    },
}
</script>
