<script>
    import { onMount } from "svelte";
    import Header from "../components/Header.svelte";
    import Popup from "../components/Popup.svelte";

	// console.log(new Date('2023-06-30T00:00:00'))
	
	let popupFlag = true;

	function setCookie(name, value, date) {
		var exdate = new Date();
		exdate.setDate(exdate.getDate() + date);
		var cookieValue = value + ((date == null) ? '' : '; expires=' + exdate.toUTCString());
		document.cookie = name + '=' + cookieValue;
	}

	const handlePopupClose = ()=>{
		popupFlag = false
    }

    const handleCloseToday = () => {
        setCookie(`stockXPopup`, 1, 1);
		popupFlag = false;
    }

	onMount(() => {
		var popupCookie = document.cookie.match('stockXPopup');
		if (popupCookie) { 
			popupFlag = false
		}
	})

</script>

{#if popupFlag}
	<Popup {handlePopupClose} {handleCloseToday}/>
{/if}
<Header/>

<div class="inner-wrap">
	<a href="/accept/agree">
		<div>
			<div class="gs25"><img src="/images/gs25_logo.png" alt=""></div>
			<div class="icon"><img src="/images/icon_box.png" alt=""></div>
			<div class="big">편의점 택배</div>
			<span class="button">접수하기</span>
		</div>
	</a>
	<a href="/login?inquire=1">
		<div>
			<div class="blank"></div>
			<div class="icon"><img src="/images/icon_location.png" alt=""></div>
			<div class="big">예약현황</div>
			<span class="button">조회하기</span>
		</div>
	</a>
</div>

<style lang="scss">
	.inner-wrap{
		margin-top:66px;
		margin-bottom:66px;
		display: flex;
		justify-content: space-between;
		align-items: center;
	}

	a{
		width:calc(50% - 30px);
		min-height: 600px;
		text-align: center;
		background:var(--blue);
		border-radius: var(--border-radius);
		display: flex;
		align-items: center;
		justify-content: center;
		letter-spacing: 2px;

		div{
			color:#fff;
			font-size:var(--font-size-big-big);
			font-weight: bold;
		}

		&:nth-of-type(2){
			background:var(--orange);
		}
	}

	.gs25{
		width:140px;
		height:48px;
		background: white;
		border-radius: 24px;
		margin:0 auto;
		transform: translateX(-110px);
		img{
			vertical-align: top;
			transform: translateY(9px);
		}
	}

	.blank{
		height:48px;
	}

	.icon{
		margin-top:46px;
		transform: translateX(9px);
	}

	.big{
		margin-top:62.7px;
	}

	span.button{
		display: inline-block;
		width:260px;
		height:80px;
		line-height: 80px;
		background: white;
		box-shadow: 0px 3px 6px rgba(0,0,0,0.51);
		border-radius: 40px;
		font-size:var(--font-size-semi-big);
		margin-top:42px;
		color:#3070B2;
		text-indent: -20px;
		position: relative;
		letter-spacing: 1px;

		&::before{
			content: '';
			position: absolute;
			top:calc(50% - 18px);
			right:calc(50% - 18px);
			width:36px;
			height: 36px;
			border-radius: 50%;
			background: #3070B2;
			transform: translateX(70px);
		}

		&::after{
			content: '';
			position: absolute;
			top:calc(50% - 10px);
			right:calc(50% - 6px);
			width:12px;
			height: 20px;
			background: url('/images/icon_arrow.png') no-repeat;
			background-size: cover;
			transform: translateX(70px);
			
		}
	}


	a:nth-of-type(2){
		span.button{
			color:#E0691D;

			&::before{
				background:#E0691D;
			}
		}
	}


	@media only screen and (max-width:1024px){
		.gs25{
			transform: translateX(0px);
		}

		a{
			width:calc(50% - 10px);
		}

		span.button{
			width:249px;
			font-size:29px;
			height:74px;
			line-height: 74px;		

			&::before{
				width:33px;
				height:33px;
				top:calc(50% - 16.5px);
				right:calc(50% - 16.5px);
			}

			&::after{
				transform: translateX(70px) scale(0.9);
			}
		}
	}

	@media only screen and (max-width:768px){
		
	}

	@media only screen and (max-width:425px){
		
		.inner-wrap{
			margin-top:29px;
			margin-bottom:29px;
			display: block;
		}

		a{
			width:100%;
			min-height: 344px;

			&:nth-of-type(2){
				margin-top:29px;
			}
		}

		.icon{
			display: none;
		}

		.big{
			margin-top:32px;
			font-size:52px;
		}

		span.button{
			display: inline-block;
			width:249px;
			height:74px;
			line-height: 74px;
			border-radius: 37px;
			font-size:29px;
			margin-top:32px;

			&::before{
				content: '';
				position: absolute;
				top:calc(50% - 18px);
				right:calc(50% - 18px);
				width:36px;
				height: 36px;
				border-radius: 50%;
				background: #3070B2;
				transform: translateX(70px);
			}

			&::after{
				content: '';
				position: absolute;
				top:calc(50% - 10px);
				right:calc(50% - 6px);
				width:12px;
				height: 20px;
				background: url('/images/icon_arrow.png') no-repeat;
				background-size: cover;
				transform: translateX(70px);
				
			}
		}

		.blank{
			display: none;
		}
	}
</style>