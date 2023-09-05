<script>
    import { onMount } from "svelte";
    import { postApi } from "../services/api";
    import { auth } from "../services/auth";

    let exchange = {
        price : 0,
        startDate : null,
        endDate : null
    }

    let lists = [];

    export let handleMask;

    const handleSave = async()=>{

        await auth();
        
        const res = await postApi({
            path:"/api/exchange/set",
            data:exchange
        });
        
        if(res.msg=='ok'){       
            const res2 = await postApi({
                path:"/api/exchange/get"
            });

            if(res2.msg=='ok'){
                lists = res2.data;
            }
        }
    }

    const handleDel = async(code)=>{

        await auth();

        const res = await postApi({
            path:"/api/exchange/del",
            data:{code:code}
        });

        if(res.msg=='ok'){
            const res2 = await postApi({
                path:"/api/exchange/get"
            });

            if(res2.msg=='ok'){
                lists = res2.data;
            }
        }

    }

    onMount(async()=>{

        await auth();

        const res = await postApi({
            path:"/api/exchange/get"
        });

        if(res.msg=='ok'){
            lists = res.data;
        }
    });

</script>

<div id="exchange">
    <div class="table">
        <div class='th'>환율</div>
        <div><input type="number" step="0.01" bind:value={exchange.price}></div>
    </div>

    <div class="table">
        <div class='th'>적용기간</div>
        <div><input type="date" bind:value={exchange.startDate}> ~ <input type="date" bind:value={exchange.endDate}></div>
    </div>
    <div class="help">공백기간이 발생하지 않도록 현재 적용기간의 마지막 날짜와 동일한 날짜로 새로운 적용기간을 시작해주세요.</div>

    <div class="button-area">
        <button type="button" on:click={handleMask}>닫기</button>
        <button type="button" on:click={handleSave}>저장</button>
    </div>

    <table>
        <tr>
            <th>고시환율</th>
            <th>적용기간</th>
            <th>삭제</th>
        </tr>
        {#each lists as item}
        <tr>
            <td>{item.price}</td>
            <td>{item.startDate} ~ {item.endDate}</td>
            <td><button type="button" on:click={handleDel(item.code)}>X</button></td>
        </tr>    
        {/each}
    </table>
</div>


<style lang="scss">
    #exchange{
        position: fixed;
        top:40%;
        left:50%;
        z-index: 1100;
        background: #fff;
        padding:20px;
        border-radius: 4px;
        transform: translate(-50%,-50%);


        .table:nth-of-type(2){
            margin-top:10px;
        }

        .button-area{
            text-align: center;
        }
    }

    .button-area{
        margin-top:10px;
        padding-left: 5px;
        button{
            margin-right:10px;
        }
    }

    .help{
        margin-top:5px;
    }

    table{
        margin-top:20px;
        width:100%;
    }

    td,th{
        border:1px solid gray;
        text-align: center;
        padding:10px;
    }
</style>