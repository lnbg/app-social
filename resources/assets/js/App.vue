<template>
    <div id="app-wrapper">
        <transition name="fade" mode="out-in">
            <router-view></router-view>
        </transition>
    </div>
</template>
<script>

export default {
    name: 'App'
}
</script>
