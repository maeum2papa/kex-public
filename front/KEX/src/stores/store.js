import { writable } from 'svelte/store';

// export const count = writable(0);

const cAlert = writable({
    flag:false,
    msg:'',
    feedback:()=>{
        
    }
})

export{
    cAlert
}