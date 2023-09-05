

<header>
    <div class="wrap">
        <div><a href="/"><img src="/images/stockx.png" alt=""><span>편의점 택배 접수 서비스</span></a></div>
        <div><img src="/images/sf_kex_logo_white.png" alt=""></div>
    </div>
</header>


<style lang="scss">
    header{
        background: var(--green);
        height:95px;

        .wrap{
            display: flex;
            justify-content: space-between;
            color:#fff;
            height:95px;
            align-items: center;

            div,
            a,
            span{
                color:#fff;
            }

            a{
                span{
                    font-size:var(--font-size-semi-small);
                    margin-left:17px;
                }
            }
        }
    }

    @media only screen and (max-width:1024px){
        header{
            height:84px;
            .wrap{
                height: 84px;
            }
        }
    }
    @media only screen and (max-width:425px){
        header{
            .wrap{
                &>div:first-of-type{
                    img{
                        width:27vw;
                    }
                }

                &>div:last-of-type{
                    transform: translateY(3px);
                    img{
                        width:20vw;
                    }
                }

                a{
                    span{
                        margin-left:4.6px;
                    }
                }
            }
        }
    }
</style>