<script>
    import { Loading } from '../stores/store';

    function closeAlert(){
        $Loading.flag = false;
    }
</script>

{#if $Loading.flag}
    <div class="lds-dual-ring"></div>
    <div class="mask"></div>
{/if}

<style>
    .lds-dual-ring {
        display: inline-block;
        width: 80px;
        height: 80px;
        position: fixed;
        top:50%;
        left:50%;
        transform: translate(-50%,-50%);
    }
    .lds-dual-ring:after {
        content: " ";
        display: block;
        width: 64px;
        height: 64px;
        margin: 8px;
        border-radius: 50%;
        border: 6px solid gray;
        border-color: gray transparent gray transparent;
        animation: lds-dual-ring 1.2s linear infinite;
    }
    @keyframes lds-dual-ring {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    .mask{
        position: fixed;
        width:100vw;
        height:100vh;
        background: rgba(255,255,255,0.1);
        top:0;
        left:0;
    }
</style>