<template>
    <div id="overlay" v-if="store.overlay">
        <div class="nms-loader">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
</template>

<script setup>
    import { store } from './../store/store'
</script>

<style scope>
#overlay{
    position: fixed;
    z-index: 10000;
    width:100%;
    height: 100%;
    background: #33333388;
    display: flex;
    justify-content: center;
    align-items: center;
}
.nms-loader {
  display: inline-block;
  position: relative;
  width: 80px;
  height: 80px;
}
.nms-loader div {
  animation: nms-loader 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
  transform-origin: 40px 40px;
}
.nms-loader div:after {
  content: " ";
  display: block;
  position: absolute;
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: #fff;
  margin: -4px 0 0 -4px;
}
.nms-loader div:nth-child(1) {
  animation-delay: -0.036s;
}
.nms-loader div:nth-child(1):after {
  top: 63px;
  left: 63px;
}
.nms-loader div:nth-child(2) {
  animation-delay: -0.072s;
}
.nms-loader div:nth-child(2):after {
  top: 68px;
  left: 56px;
}
.nms-loader div:nth-child(3) {
  animation-delay: -0.108s;
}
.nms-loader div:nth-child(3):after {
  top: 71px;
  left: 48px;
}
.nms-loader div:nth-child(4) {
  animation-delay: -0.144s;
}
.nms-loader div:nth-child(4):after {
  top: 72px;
  left: 40px;
}
.nms-loader div:nth-child(5) {
  animation-delay: -0.18s;
}
.nms-loader div:nth-child(5):after {
  top: 71px;
  left: 32px;
}
.nms-loader div:nth-child(6) {
  animation-delay: -0.216s;
}
.nms-loader div:nth-child(6):after {
  top: 68px;
  left: 24px;
}
.nms-loader div:nth-child(7) {
  animation-delay: -0.252s;
}
.nms-loader div:nth-child(7):after {
  top: 63px;
  left: 17px;
}
.nms-loader div:nth-child(8) {
  animation-delay: -0.288s;
}
.nms-loader div:nth-child(8):after {
  top: 56px;
  left: 12px;
}
@keyframes nms-loader {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

</style>