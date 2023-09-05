<script>
    import JsBarcode from 'jsbarcode'
    import { onMount } from "svelte";

    let orderNumber = '';

    onMount(()=>{

        const urlParams = new URL(location.href).searchParams;
        const barcode = urlParams.get('d');
        orderNumber = urlParams.get('o');
        
        JsBarcode("#barcode", barcode);
        

        setTimeout(()=>{
            print();
            //self.close();
        },5)
    })
</script>


<div class="wrap">
    <div>{orderNumber}</div>
    <svg id="barcode"></svg>
</div>

<style>
    .wrap{
        position: fixed;
        top:50%;
        left:50%;
        text-align: center;
        transform: translate(-50%,-50%);
    }
</style>