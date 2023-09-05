<script>
    import { onMount } from "svelte";
    import Header from "../../components/Header.svelte";
	import { cAlert } from "../../stores/store";
    import { postApi } from "../../services/api";
    import { auth } from "../../services/auth";

	let cargos = [];
	let name = '';

	const handleMain = ()=>{
		location.replace('/');
	}

	const handleSFFind = ()=>{
		window.open('https://www.sf-international.com/kr/ko/dynamic_function/waybill/');
	}

	onMount(async()=>{
		
		const res = await auth();

		if(res.msg=='ok'){
			
			const res2 = await postApi({
				path:"/api/inquire"
			});
			
			if(res2.msg=='ok'){
				cargos = res2.list;
				name = res2.name;
			}

		}else{
			location.replace('/');
		}

	});

</script>

<Header/>

<div class="inner-wrap">

	<h1><span>{name}님, </span>예약 현황 입니다.</h1>

	{#if cargos.length>0}
	

	<div class="table">
			<h2>최근 예약<span class="help">3개월 이내 예약만 조회 가능합니다.</span></h2>

			<div class="table-head">
				<div>NO.</div>
				<div>SF-KEX 운송장 번호</div>
				<div>승인번호 번호</div>
				<div>예약일</div>
				<div>화물상태</div>
			</div>
		{#each cargos as item,i}
			<div class="table-body">
				<div>
					<div>{item.no}</div>
				</div>
				<div>
					<div>{item.SFNumber}</div>
				</div>
				<div>
					<div>{item.reservationNumber}</div>
				</div>
				<div>
					<div>{item.date}</div>
				</div>
				<div>
					<div>{item.status}</div>
				</div>
			</div>
		{/each}

		<div class="table-body-mobile">
			{#each cargos as item,i}
			<div>
				<table>
					<tr>
						<td>
							SF-KEX 송장번호
						</td>
						<td>
							{item.SFNumber}
						</td>
					</tr>
					<tr>
						<td>
							승인번호
						</td>
						<td>	
							{item.reservationNumber}
						</td>
					</tr>
					<tr>
						<td>
							예약일
						</td>
						<td>
							{item.date}
						</td>
					</tr>
					<tr>
						<td>
							화물상태
						</td>
						<td>
							{item.status}
						</td>
					</tr>
				</table>
			</div>
			{/each}
		</div>

	</div>

	
	


	<div class="button-area">
		<button type="button" on:click={handleMain}>처음으로</button>
		<button type="button" on:click={handleSFFind}>화물 위치 확인하기</button>
	</div>
	{/if}

</div>


<style lang="scss">
	.inner-wrap{
		margin-top:95px;
		padding-bottom:148px;
	}

	h1{
		font-size:var(--font-size-big-big);
		text-align: center;
		font-weight: 400;
	}

	.button-area{
		text-align: center;
		margin-top:67px;
	}

	.table{
		margin-top:44px;
	}

	.table > .table-body{
		display: flex;
		margin-top:0;
		border-top:0;

		&>div{
			width:50%;
			text-align: center;
			color:var(--font-gray);
			padding-top:25px;
			padding-bottom:25px;


			&:nth-of-type(1){
				width:15%;
			}
			&:nth-of-type(5){
				width:25%;
			}

			*{
				width:100%;
				font-weight: 400;
				color:var(--font-gray);
			}
		}
	}

	.table > .table-head{
		display: flex;
		border-top:3px solid var(--black);
		border-bottom:2px solid var(--border-color-gray);
		margin-top:27.5px;

		&>div{
			width:50%;
			text-align: center;
			font-size:var(--font-size-normal);
			color:var(--font-gray);
			padding-top: 27px;
    		padding-bottom: 27px;
			font-weight: 500;

			&:nth-of-type(1){
				width:15%;
			}
			&:nth-of-type(5){
				width:25%;
			}
		}
	}
	button{
		margin:0 10px;
	}

	.help{
		color:var(--orange);
		display: inline-block;
		margin-left:16px;
		font-size: var(--font-size-small);
	}


	.table-body-mobile{
		display: none;
		margin-top:27.5px;
		border-top:3px solid var(--black);
		
		&>div{
			border-bottom:2px solid var(--border-color-gray);
			padding:13px 0px;
		}

		table{
			width:100%;
			td{
				font-size:var(--font-size-normal);
				padding:14.5px 0px;
				color:var(--font-gray);

				&:nth-child(1){
					width:211px;
				}
			}
		}
	}
	
@media only screen and (max-width:1024px){
	.table > .table-head{
		display: none;
	}
	.table > .table-body{
		display: none;
	}

	.table-body-mobile{
		display: block;
	}
}

@media only screen and (max-width:768px){
    
}

@media only screen and (max-width:425px){

	.inner-wrap{
		margin-top:53px;
		padding-bottom:122px;
	}

	.table{
		margin-top:38.9px;
	}
    
	.table-body-mobile{
		margin-top:17.5px;
		
		&>div{
			padding:11px 0px;
		}

		table{
			
			td{
				padding:9px 0px;

				&:nth-child(1){
					width:137px;
				}
			}
		}
	}

	.button-area{
		margin-top:45.4px;
	}

	button{
		margin:0;
		margin-bottom: 10px;
	}

	h1{
		span{
			display: none;
		}
	}

}
</style>