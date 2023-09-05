<script>
    import { onMount } from "svelte";
    import Header from "../../../components/Header.svelte";
	import { cAlert } from "../../../stores/store";
    import { postApi } from "../../../services/api";
    import { auth } from "../../../services/auth";

	let cargos = [];

	const handleMain = ()=>{
		location.replace('/');
	}

	const handleGSFind = ()=>{
		window.open('https://www.cvsnet.co.kr/service/search-store/index.do');
	}

	onMount(async()=>{

		const res = await auth();

		if(res.msg=='ok'){

			const res2 = await postApi(
				{
					path:"/api/success"
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

	<h1>발송 예정 화물</h1>

	{#if cargos.length>0}

	<p>예약접수가 완료되었습니다.
		아래 접수 정보를 참고하셔서
		가까운 GS편의점에서 방문 발송 진행을 부탁 드립니다.</p>

	
	<div class="table">
			<div class="table-head">
				<div>SF-KEX 운송장 번호</div>
				<div>승인번호</div>
			</div>
		{#each cargos as item,i}
			<div class="table-body">
				<div>
					<div>{item.SFNumber}</div>
				</div>
				<div>
					<div>{item.reservationNumber}</div>
				</div>
			</div>
		{/each}
	</div>


	<div class="button-area">
		<button type="button" on:click={handleMain}>처음으로</button>
		<button type="button" on:click={handleGSFind}>가까운 GS편의점 확인 바로가기</button>
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

	p{
		font-size:var(--font-size-big);
		margin-top:60px;
		white-space: pre-line;
		line-height: 46px;
		color:var(--font-gray);
	}

	.button-area{
		text-align: center;
		margin-top:67px;
	}

	.table > .table-body{
		display: flex;
		margin-top:0;
		border-top:0;

		&>div{
			width:50%;
			text-align: center;
			*{
				width:auto;
				font-weight: 400;
				width:100%;
				text-align: center;
			}
		}
	}

	.table > .table-head{
		display: flex;
		border-top:3px solid var(--black);
		margin-top:60px;

		&>div{
			width:50%;
			text-align: center;
			font-size:var(--font-size-normal);
			padding-top: 18.5px;
    		padding-bottom: 18.5px;
			border-bottom:3px solid var(--black);
			font-weight: 500;
		}
	}
	button{
		margin:0 10px;
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

			&>div{

				padding-top: 14px;
				padding-bottom: 14px;
				
				*{
					width:100%;
				}
				&:first-of-type{
					width: 168px;
				}
				
			}
		}

		p{
			font-size:var(--font-size-normal);
			margin-top:45px;
			white-space: pre-line;
			line-height: 24px;
		}


		.table > .table-head{
			display: flex;
			margin-top:45px;

			&>div{
				padding-top: 14px;
				padding-bottom: 14px;

				&:first-of-type{
					width: 168px;
				}
			}
		}

	}
</style>