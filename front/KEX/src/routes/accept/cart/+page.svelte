<script>
    import { onMount } from "svelte";
    import Header from "../../../components/Header.svelte";
	import { won } from "../../../services/format";
	import { cAlert } from "../../../stores/store";
	import { postApi } from "../../../services/api";
	import { auth } from "../../../services/auth";

	let cargos = [];
	let mobileLast = '';


	const handleReset = async()=>{

		const res  = await postApi({
			path:'/api/cart/reset',
			data:{mobileLast:mobileLast}
		});

		if(res.msg=='ok'){
			$cAlert = {flag:true,msg:'화물정보를 초기화하였습니다. SF운송장 찾기로 이동합니다.',feedback:()=>{location.replace('/accept/search')}}
		}
	}

	const handleAddSearch = ()=>{
		location.replace('/accept/search');
	}

	const handleSuccess = async()=>{
		
		const res = await postApi(
			{
				path:"/api/register",
				data:{mobileLast:mobileLast}
			}
		)
		
		if(res.msg=='ok'){
			location.replace('/accept/success');
		}else{
			$cAlert = {flag:true,msg:'네트워크 장애로 문제가 발생하였습니다. 잠시 후 다시 시도해 주세요.',feedback:()=>{}}
		}
	}

	onMount(async()=>{
		const res = await auth();
		
		if(res.msg=='ok'){

			mobileLast = res.mobile.slice(-4);
			
			const res2 = await postApi(
				{
					path:"/api/cart",
					data:{mobileLast:mobileLast}
				}
			);
			
			if(res2.msg=='ok'){
				cargos = res2.list;
			}

		}else{
			location.replace("/");
		}
	});

</script>

<Header/>

<div class="inner-wrap">

	<h1>화물정보</h1>

	{#if cargos.length>0}
	<div class="table">
		{#each cargos as item,i}
			<div class="table-body">
				<div>
					<div>운송장 번호</div>
					<div>{item.SFNumber}</div>
				</div>
				<div>
					<div>물품</div>
					<div>{item.product}</div>
				</div>
				<div>
					<div>상품수량</div>
					<div>{won(item.ea)}</div>
				</div>
				<div>
					<div>물품가액</div>
					<div>{won(item.price)}원</div>
				</div>
			</div>
		{/each}
	</div>


	<div class="button-area">
		<button type="button" on:click={handleReset}>초기화</button>

		<button type="button" on:click={handleAddSearch}>택배 접수 추가</button>

		<button type="button" on:click={handleSuccess}>예약 접수 완료</button>
	</div>
	{/if}

</div>


<style lang="scss">
	.inner-wrap{
		margin-top:95px;
		margin-bottom:148px;
	}

	h1{
		font-size:var(--font-size-big-big);
		text-align: center;
	}

	.button-area{
		text-align: center;
		margin-top:67px;
	}

	.table > .table-body{
		margin-top:60px;
	}

	button{
		margin:0 5px;
	}

	@media only screen and (max-width:425px){
		.inner-wrap{
			margin-top:53px;
			margin-bottom:122px;
		}

		h1{
			font-weight: 500;
		}

		.button-area{
			text-align: center;
			margin-top:45px;
		}

		button{
			margin:0;
			margin-bottom:10px;
		}

		.table > .table-body{
			margin-top:45px;
		}
	}
</style>