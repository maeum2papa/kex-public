<script>
	import "../css/common.min.css";
	import Alert2 from '../components/Alert2.svelte'
</script>

<div class="app">
	
	<main>
		<slot />
	</main>

	<footer>
			<div class="info-area">
				<div class="inner-wrap">
					<div>
						<div>
							<img src="/images/sf_kex_logo.png" alt="" class="logo">
						</div>
						<div>
							<p>상호명 : 케이이엑스익스프레스코리아 주식회사
								대표자명 : LAU KIN PUI(라우킨푸이)
								사업자등록번호 : 245-81-02745
								주소 : 서울특별시 영등포구 국회대로68길 17, 3층(여의도동)</p>
						</div>
					</div>
					<div>
						<div>
							<div class="info-area-tel-title">KEX CS 핫라인</div>
							<div class="info-area-tel">080-393-1111</div>
						</div>
						<div>
							<p>월요일-금요일: 09:00-18:30
								토요일: 09:00-12:30
								일요일 & 공휴일: 휴무</p>
						</div>
					</div>
				</div>
			</div>
			<div class="copyright-area">
				<div class="wrap">
					<div>
						<a href="/">이용약관</a>
						<a href="/" class="policy">개인정보처리방침</a>
					</div>
					<div>
						© KEX EXPRESS ALL RIGHTS RESERVED.
					</div>
				</div>
			</div>
	</footer>
</div>

<Alert2/>


<style lang="scss">
	main{
		min-height:calc(100vh - 247px);
	}

	@media only screen and (max-width:1024px){
		main{
			min-height:calc(100vh - 300px);
		}
	}

	footer{
		background:var(--light-gray);

		.info-area{
			padding-top:40px;
			padding-bottom:45px;
			&>div{//inner-wrap
				display: flex;
				justify-content: space-between;
				align-items: flex-start;

				&>div{
					display: flex;
					align-items: flex-start;
					width:calc(50% - 30px);

					.info-area-tel-title{
						font-size: var(--font-size-small);
						font-weight: 500;
					}

					.info-area-tel{
						font-size:var(--font-size-big);
						font-weight: 500;
						line-height: 43px;
						color:var(--orange);
					}
				}
			}
			p{
				white-space: pre-line;
				font-size:var(--font-size-small-small-small);
				color:var(--font-light-gray);
				line-height: 23px;
				margin-left:3.5vw;
			}
		}

		.copyright-area{
			background:var(--black);
			
			&>div{
				display: flex;
				justify-content: space-between;
				height:70px;
				align-items: center;
			}

			*{
				color:#fff;
			}

			.policy{
				margin-left:25px;
			}
		}

		img.logo{
			width:144px;
		}
	}


	@media only screen and (max-width:1024px){
		footer .info-area .inner-wrap{
			display: block;

			&>div{
				width:100%;

				&:nth-of-type(2){
					margin-top:35px;
				}

				&>div{
					&:nth-of-type(1){
						width:265px;
					}
				}
			}
		}
	}

	@media only screen and (max-width:768px){
		
	}

	@media only screen and (max-width:425px){


		footer{

			.info-area{
				padding:23px 0px;
				&>div{//inner-wrap
					display: block;

					&>div{
						display: block;
						&:last-of-type{
							margin-left:0;
						}

						.info-area-tel-title{
							font-size:18px;
						}
					}
				}
				p{
					margin-top:3px;
					margin-left:0;
				}
			}

			.copyright-area{
				padding:30px 0px;
				&>div{
					display: block;
					height:auto;

					&>div:last-of-type{
						margin-top:17px;
					}
				}

				*{
					font-size:17px;
				}

				.policy{
					margin-left:25px;
				}
			}

			img.logo{
				width:144px;
			}
		}
	}

</style>