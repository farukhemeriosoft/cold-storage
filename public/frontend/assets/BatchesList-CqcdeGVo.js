import{A as e,C as t,D as n,E as r,G as i,K as a,L as o,M as s,N as c,d as l,g as u,i as d,j as ee,m as f,s as p,t as te,u as m,v as h,w as g,y as _,z as v}from"./index-BlF1ZIv-.js";import{b as ne}from"./useDarkMode-BPtTVGNh.js";import{b as re}from"./MegaSearch-DluE57NX.js";import{b as ie}from"./LanguageSwitcher-BCL_-mgC.js";const ae={class:`min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300`},oe={class:`bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700`},se={class:`w-full px-[2%]`},ce={class:`flex justify-between items-center h-16`},le={class:`flex items-center space-x-4`},ue={class:`hidden md:block w-80`},de={key:0,class:`h-5 w-5`,fill:`none`,viewBox:`0 0 24 24`,stroke:`currentColor`},fe={key:1,class:`h-5 w-5`,fill:`none`,viewBox:`0 0 24 24`,stroke:`currentColor`},pe={class:`w-full py-8 px-[2%]`},y={class:`mb-6`},b={class:`flex space-x-3`},x={class:`flex justify-between items-center mb-8`},S={key:0,class:`flex justify-center items-center py-12`},C={key:1,class:`bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg mb-6`},w={key:2,class:`bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden`},T={class:`overflow-x-auto`},E={class:`min-w-full divide-y divide-gray-200 dark:divide-gray-700`},D={class:`bg-gray-50 dark:bg-gray-700 sticky top-0 z-10`},O={class:`flex items-center space-x-1`},k={class:`flex flex-col`},A={class:`bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700`},j={key:0,class:`bg-white dark:bg-gray-800`},M={class:`px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white`},N={class:`px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400`},P={class:`px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white`},F={class:`flex flex-col`},I={class:`font-medium`},L={class:`text-xs text-gray-500 dark:text-gray-400`},R={class:`px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white`},z={class:`font-medium`},B={class:`px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white`},me={class:`flex flex-col`},he={class:`font-medium`},ge={class:`text-xs font-semibold text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-900 px-2 py-1 rounded-full inline-block w-fit`},_e={class:`px-6 py-4 whitespace-nowrap text-sm font-medium`},ve={class:`px-6 py-4 whitespace-nowrap text-sm font-medium`},ye={class:`flex items-center space-x-2`},be=[`onClick`],V=[`onClick`],xe={key:0,class:`bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6`},Se={class:`flex items-center justify-between`},Ce={class:`flex-1 flex justify-between sm:hidden`},we=[`disabled`],Te=[`disabled`],Ee={class:`hidden sm:flex-1 sm:flex sm:items-center sm:justify-between`},De={class:`text-sm text-gray-700 dark:text-gray-300`},Oe={class:`font-medium`},ke={class:`font-medium`},Ae={class:`font-medium`},je={class:`relative z-0 inline-flex rounded-md shadow-sm -space-x-px`,"aria-label":`Pagination`},Me=[`disabled`],Ne=[`onClick`],Pe=[`disabled`],Fe={__name:`BatchesList`,setup(Fe){let H=l(),Ie=ee(),{isDarkMode:Le,toggleDarkMode:U}=ne(),W=a([]),G=a({}),K=a(!1),q=a(``),J=a(`asc`),Re=te(()=>W.value.map((e,t)=>({...e,serialNumber:t+1})));e(async()=>{let e=localStorage.getItem(`token`);console.log(`Auth token:`,e?`Present`:`Missing`);try{console.log(`Testing API connection...`);let e=await f.getBatches(1);console.log(`Direct API test successful:`,e)}catch(e){console.error(`Direct API test failed:`,e)}Y()});let Y=async(e=1)=>{K.value=!0,q.value=``;try{let t=await f.getBatches(e);console.log(`Lots API Response:`,t.data),console.log(`First batch data:`,t.data.data?.data?.[0]);let n=t.data.data||t.data;n.data?(W.value=n.data,G.value={current_page:n.current_page,last_page:n.last_page,from:n.from,to:n.to,total:n.total,prev_page_url:n.prev_page_url,next_page_url:n.next_page_url}):Array.isArray(n)?(W.value=n,G.value={current_page:1,last_page:1,from:1,to:n.length,total:n.length,prev_page_url:null,next_page_url:null}):(W.value=[n],G.value={current_page:1,last_page:1,from:1,to:1,total:1,prev_page_url:null,next_page_url:null}),X()}catch(e){console.error(`Error loading batches:`,e),console.error(`Error details:`,e.response?.data),q.value=e.response?.data?.message||`Failed to load batches. Please try again.`}finally{K.value=!1}},ze=()=>{J.value=J.value===`asc`?`desc`:`asc`,X()},X=()=>{W.value.sort((e,t)=>{let n=new Date(e.created_at),r=new Date(t.created_at);return J.value===`asc`?n-r:r-n})},Be=()=>{let e=G.value.current_page,t=G.value.last_page,n=[],r=[];for(let r=Math.max(2,e-2);r<=Math.min(t-1,e+2);r++)n.push(r);return e-2>2?r.push(1,`...`):r.push(1),r.push(...n),e+2<t-1?r.push(`...`,t):r.push(t),r.filter((e,t,n)=>n.indexOf(e)===t)},Z=e=>new Intl.NumberFormat(`en-PK`).format(e),Q=e=>e.baskets_count||e.baskets?.length||e.total_baskets||e.basket_count||(e.baskets&&Array.isArray(e.baskets)?e.baskets.length:0)||0,$=e=>{let t=Q(e),n=parseFloat(e.unit_price)||0,r=t*n;return Z(r)},Ve=e=>{let t={batchId:e.id,customerName:e.customer?.full_name||`N/A`,customerCNIC:e.customer?.cnic_number||`N/A`,customerPhone:e.customer?.phone_number||`N/A`,customerAddress:e.customer?.address||`N/A`,unitPrice:e.unit_price,basketCount:Q(e),totalPrice:$(e),createdDate:e.created_at,invoiceDate:new Date().toLocaleDateString(`en-PK`)},n=window.open(``,`_blank`,`width=800,height=600`),r=`
    <!DOCTYPE html>
    <html dir="${u.value?`rtl`:`ltr`}" lang="${u.value?`ur`:`en`}">
    <head>
      <title>${d(`invoice.title`)} - ${d(`invoice.batchId`)} ${e.id}</title>
      <style>
        body {
          font-family: ${u.value?`Noto Sans Urdu, Arial, sans-serif`:`Arial, sans-serif`};
          margin: 20px;
          direction: ${u.value?`rtl`:`ltr`};
        }
        .header { text-align: center; margin-bottom: 30px; }
        .invoice-title { font-size: 24px; font-weight: bold; color: #333; }
        .invoice-details { margin-bottom: 20px; }
        .customer-info, .batch-info { margin-bottom: 15px; }
        .info-row { margin-bottom: 5px; }
        .label { font-weight: bold; display: inline-block; width: ${u.value?`140px`:`120px`}; }
        .total-section { background: #f5f5f5; padding: 15px; margin-top: 20px; }
        .total-amount { font-size: 18px; font-weight: bold; color: #2d3748; }
        .footer { margin-top: 30px; text-align: center; color: #666; }
        h3 { margin-bottom: 10px; color: #2d3748; }
      </style>
    </head>
    <body>
      <div class="header">
        <div class="invoice-title">${d(`invoice.title`)}</div>
        <div>${d(`invoice.date`)}: ${t.invoiceDate}</div>
      </div>

      <div class="invoice-details">
        <div class="customer-info">
          <h3>${d(`invoice.customerInfo`)}</h3>
          <div class="info-row"><span class="label">${d(`invoice.name`)}:</span> ${t.customerName}</div>
          <div class="info-row"><span class="label">${d(`invoice.cnic`)}:</span> ${t.customerCNIC}</div>
          <div class="info-row"><span class="label">${d(`invoice.phone`)}:</span> ${t.customerPhone}</div>
          <div class="info-row"><span class="label">${d(`invoice.address`)}:</span> ${t.customerAddress}</div>
        </div>

        <div class="batch-info">
          <h3>${d(`invoice.batchInfo`)}</h3>
          <div class="info-row"><span class="label">${d(`invoice.batchId`)}:</span> ${t.batchId}</div>
          <div class="info-row"><span class="label">${d(`invoice.createdDate`)}:</span> ${new Date(t.createdDate).toLocaleDateString(`en-PK`)}</div>
          <div class="info-row"><span class="label">${d(`invoice.unitPrice`)}:</span> PKR ${Z(t.unitPrice)}</div>
          <div class="info-row"><span class="label">${d(`invoice.basketCount`)}:</span> ${t.basketCount}</div>
        </div>
      </div>

      <div class="total-section">
        <div class="total-amount">${d(`invoice.totalAmount`)}: PKR ${t.totalPrice}</div>
      </div>

      <div class="footer">
        <p>${d(`invoice.thankYou`)}</p>
        <p>Powered by Emeriosoft</p>
      </div>
    </body>
    </html>
  `;n.document.write(r),n.document.close()},He=e=>{let t={batchId:e.id,customerName:e.customer?.full_name||`N/A`,customerCNIC:e.customer?.cnic_number||`N/A`,customerPhone:e.customer?.phone_number||`N/A`,customerAddress:e.customer?.address||`N/A`,unitPrice:e.unit_price,basketCount:Q(e),totalPrice:$(e),createdDate:e.created_at,invoiceDate:new Date().toLocaleDateString(`en-PK`)},n=`
    <!DOCTYPE html>
    <html dir="${u.value?`rtl`:`ltr`}" lang="${u.value?`ur`:`en`}">
    <head>
      <title>${d(`invoice.title`)} - ${d(`invoice.batchId`)} ${e.id}</title>
      <style>
        body {
          font-family: ${u.value?`Noto Sans Urdu, Arial, sans-serif`:`Arial, sans-serif`};
          margin: 20px;
          direction: ${u.value?`rtl`:`ltr`};
        }
        .header { text-align: center; margin-bottom: 30px; }
        .invoice-title { font-size: 24px; font-weight: bold; color: #333; }
        .invoice-details { margin-bottom: 20px; }
        .customer-info, .batch-info { margin-bottom: 15px; }
        .info-row { margin-bottom: 5px; }
        .label { font-weight: bold; display: inline-block; width: ${u.value?`140px`:`120px`}; }
        .total-section { background: #f5f5f5; padding: 15px; margin-top: 20px; }
        .total-amount { font-size: 18px; font-weight: bold; color: #2d3748; }
        .footer { margin-top: 30px; text-align: center; color: #666; }
        h3 { margin-bottom: 10px; color: #2d3748; }
        @media print {
          body { margin: 0; }
          .no-print { display: none; }
        }
      </style>
    </head>
    <body>
      <div class="header">
        <div class="invoice-title">${d(`invoice.title`)}</div>
        <div>${d(`invoice.date`)}: ${t.invoiceDate}</div>
      </div>

      <div class="invoice-details">
        <div class="customer-info">
          <h3>${d(`invoice.customerInfo`)}</h3>
          <div class="info-row"><span class="label">${d(`invoice.name`)}:</span> ${t.customerName}</div>
          <div class="info-row"><span class="label">${d(`invoice.cnic`)}:</span> ${t.customerCNIC}</div>
          <div class="info-row"><span class="label">${d(`invoice.phone`)}:</span> ${t.customerPhone}</div>
          <div class="info-row"><span class="label">${d(`invoice.address`)}:</span> ${t.customerAddress}</div>
        </div>

        <div class="batch-info">
          <h3>${d(`invoice.batchInfo`)}</h3>
          <div class="info-row"><span class="label">${d(`invoice.batchId`)}:</span> ${t.batchId}</div>
          <div class="info-row"><span class="label">${d(`invoice.createdDate`)}:</span> ${new Date(t.createdDate).toLocaleDateString(`en-PK`)}</div>
          <div class="info-row"><span class="label">${d(`invoice.unitPrice`)}:</span> PKR ${Z(t.unitPrice)}</div>
          <div class="info-row"><span class="label">${d(`invoice.basketCount`)}:</span> ${t.basketCount}</div>
        </div>
      </div>

      <div class="total-section">
        <div class="total-amount">${d(`invoice.totalAmount`)}: PKR ${t.totalPrice}</div>
      </div>

      <div class="footer">
        <p>${d(`invoice.thankYou`)}</p>
        <p>Powered by Emeriosoft</p>
      </div>
    </body>
    </html>
  `,r=window.open(``,`_blank`,`width=800,height=600`);r.document.write(n),r.document.close(),r.onload=()=>{r.print(),r.close()}},Ue=e=>new Date(e).toLocaleDateString(`en-PK`,{year:`numeric`,month:`short`,day:`numeric`}),We=()=>{Ie.logout(),H.push(`/login`)};return(e,a)=>{let l=r(`router-link`);return t(),g(`div`,ae,[m(`nav`,oe,[m(`div`,se,[m(`div`,ce,[a[7]||=m(`div`,{class:`flex items-center`},[m(`div`,{class:`flex-shrink-0`},[m(`h1`,{class:`text-2xl font-bold text-gray-900 dark:text-white`},`A & S Cold Storage`)])],-1),m(`div`,le,[m(`div`,ue,[v(re)]),m(`button`,{onClick:a[0]||=(...e)=>o(U)&&o(U)(...e),class:`p-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200`},[o(Le)?(t(),g(`svg`,fe,[...a[6]||=[m(`path`,{"stroke-linecap":`round`,"stroke-linejoin":`round`,"stroke-width":`2`,d:`M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z`},null,-1)]])):(t(),g(`svg`,de,[...a[5]||=[m(`path`,{"stroke-linecap":`round`,"stroke-linejoin":`round`,"stroke-width":`2`,d:`M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z`},null,-1)]]))]),v(ie),m(`button`,{onClick:We,class:`px-4 py-2 bg-gradient-to-r from-red-400 to-red-500 text-white rounded-lg hover:from-red-500 hover:to-red-600 transition-all duration-200 font-medium`},` Logout `)])])])]),m(`div`,pe,[m(`div`,y,[m(`div`,b,[v(l,{to:`/dashboard`,class:`inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors duration-200`},{default:i(()=>[...a[8]||=[m(`svg`,{class:`w-4 h-4 mr-2`,fill:`none`,viewBox:`0 0 24 24`,stroke:`currentColor`},[m(`path`,{"stroke-linecap":`round`,"stroke-linejoin":`round`,"stroke-width":`2`,d:`M10 19l-7-7m0 0l7-7m-7 7h18`})],-1),_(` Back to Dashboard `,-1)]]),_:1}),v(l,{to:`/basket-dispatch`,class:`inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200`},{default:i(()=>[...a[9]||=[m(`svg`,{class:`w-4 h-4 mr-2`,fill:`none`,stroke:`currentColor`,viewBox:`0 0 24 24`},[m(`path`,{"stroke-linecap":`round`,"stroke-linejoin":`round`,"stroke-width":`2`,d:`M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4`})],-1),_(` Basket Dispatch `,-1)]]),_:1})])]),m(`div`,x,[a[11]||=m(`div`,null,[m(`h2`,{class:`text-3xl font-bold text-gray-900 dark:text-white mb-2`},`All Lots`),m(`p`,{class:`text-gray-600 dark:text-gray-400`},`View and manage all batch records`)],-1),v(l,{to:`/new-batch`,class:`inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-lg hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 font-medium`},{default:i(()=>[...a[10]||=[m(`svg`,{class:`w-5 h-5 mr-2`,fill:`none`,viewBox:`0 0 24 24`,stroke:`currentColor`},[m(`path`,{"stroke-linecap":`round`,"stroke-linejoin":`round`,"stroke-width":`2`,d:`M12 6v6m0 0v6m0-6h6m-6 0H6`})],-1),_(` Add New Lot `,-1)]]),_:1})]),K.value?(t(),g(`div`,S,[...a[12]||=[m(`div`,{class:`animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500`},null,-1)]])):q.value?(t(),g(`div`,C,c(q.value),1)):(t(),g(`div`,w,[m(`div`,T,[m(`table`,E,[m(`thead`,D,[m(`tr`,null,[m(`th`,{class:`px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200`,onClick:ze},[m(`div`,O,[a[15]||=m(`span`,null,`Sno`,-1),m(`div`,k,[(t(),g(`svg`,{class:s([`w-3 h-3`,J.value===`asc`?`text-blue-500`:`text-gray-400`]),fill:`currentColor`,viewBox:`0 0 20 20`},[...a[13]||=[m(`path`,{"fill-rule":`evenodd`,d:`M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z`,"clip-rule":`evenodd`},null,-1)]],2)),(t(),g(`svg`,{class:s([`w-3 h-3 -mt-1`,J.value===`desc`?`text-blue-500`:`text-gray-400`]),fill:`currentColor`,viewBox:`0 0 20 20`},[...a[14]||=[m(`path`,{"fill-rule":`evenodd`,d:`M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z`,"clip-rule":`evenodd`},null,-1)]],2))])])]),a[16]||=m(`th`,{class:`px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider`},` Created Date `,-1),a[17]||=m(`th`,{class:`px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider`},` Customer `,-1),a[18]||=m(`th`,{class:`px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider`},` Unit Price (PKR) `,-1),a[19]||=m(`th`,{class:`px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider`},` Total Price `,-1),a[20]||=m(`th`,{class:`px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider`},` Actions `,-1),a[21]||=m(`th`,{class:`px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider`},` Invoice `,-1)])]),m(`tbody`,A,[W.value.length===0?(t(),g(`tr`,j,[...a[22]||=[m(`td`,{colspan:`7`,class:`px-6 py-12 text-center text-gray-500 dark:text-gray-400`},[m(`div`,{class:`flex flex-col items-center`},[m(`svg`,{class:`w-12 h-12 text-gray-400 dark:text-gray-500 mb-4`,fill:`none`,viewBox:`0 0 24 24`,stroke:`currentColor`},[m(`path`,{"stroke-linecap":`round`,"stroke-linejoin":`round`,"stroke-width":`2`,d:`M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4`})]),m(`p`,{class:`text-lg font-medium`},`No batches found`),m(`p`,{class:`text-sm`},`Get started by creating your first batch`)])],-1)]])):h(``,!0),(t(!0),g(p,null,n(Re.value,e=>(t(),g(`tr`,{key:e.id,class:`bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200`},[m(`td`,M,c(e.serialNumber),1),m(`td`,N,c(Ue(e.created_at)),1),m(`td`,P,[m(`div`,F,[m(`span`,I,c(e.customer?.full_name||`N/A`),1),m(`span`,L,c(e.customer?.cnic_number||`N/A`),1)])]),m(`td`,R,[m(`span`,z,`PKR `+c(Z(e.unit_price)),1)]),m(`td`,B,[m(`div`,me,[m(`span`,he,`PKR `+c($(e)),1),m(`span`,ge,c(Q(e))+` basket`+c(Q(e)===1?``:`s`),1)])]),m(`td`,_e,[v(l,{to:`/add-baskets-to-batch/${e.id}`,class:`text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 transition-colors duration-200`},{default:i(()=>[...a[23]||=[_(` Add Baskets `,-1)]]),_:1},8,[`to`])]),m(`td`,ve,[m(`div`,ye,[m(`button`,{onClick:t=>Ve(e),class:`text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 transition-colors duration-200 p-1 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20`,title:`View Invoice`},[...a[24]||=[m(`svg`,{class:`h-5 w-5`,fill:`none`,viewBox:`0 0 24 24`,stroke:`currentColor`},[m(`path`,{"stroke-linecap":`round`,"stroke-linejoin":`round`,"stroke-width":`2`,d:`M15 12a3 3 0 11-6 0 3 3 0 016 0z`}),m(`path`,{"stroke-linecap":`round`,"stroke-linejoin":`round`,"stroke-width":`2`,d:`M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z`})],-1)]],8,be),m(`button`,{onClick:t=>He(e),class:`text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300 transition-colors duration-200 p-1 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700`,title:`Print Invoice`},[...a[25]||=[m(`svg`,{class:`h-5 w-5`,fill:`none`,viewBox:`0 0 24 24`,stroke:`currentColor`},[m(`path`,{"stroke-linecap":`round`,"stroke-linejoin":`round`,"stroke-width":`2`,d:`M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z`})],-1)]],8,V)])])]))),128))])])]),G.value.last_page>1?(t(),g(`div`,xe,[m(`div`,Se,[m(`div`,Ce,[m(`button`,{onClick:a[1]||=e=>Y(G.value.current_page-1),disabled:!G.value.prev_page_url,class:`relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed`},` Previous `,8,we),m(`button`,{onClick:a[2]||=e=>Y(G.value.current_page+1),disabled:!G.value.next_page_url,class:`ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed`},` Next `,8,Te)]),m(`div`,Ee,[m(`div`,null,[m(`p`,De,[a[26]||=_(` Showing `,-1),m(`span`,Oe,c(G.value.from),1),a[27]||=_(` to `,-1),m(`span`,ke,c(G.value.to),1),a[28]||=_(` of `,-1),m(`span`,Ae,c(G.value.total),1),a[29]||=_(` results `,-1)])]),m(`div`,null,[m(`nav`,je,[m(`button`,{onClick:a[3]||=e=>Y(G.value.current_page-1),disabled:!G.value.prev_page_url,class:`relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed`},[...a[30]||=[m(`span`,{class:`sr-only`},`Previous`,-1),m(`svg`,{class:`h-5 w-5`,fill:`currentColor`,viewBox:`0 0 20 20`},[m(`path`,{"fill-rule":`evenodd`,d:`M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z`,"clip-rule":`evenodd`})],-1)]],8,Me),(t(!0),g(p,null,n(Be(),e=>(t(),g(`button`,{key:e,onClick:t=>Y(e),class:s([e===G.value.current_page?`z-10 bg-blue-50 dark:bg-blue-900 border-blue-500 dark:border-blue-400 text-blue-600 dark:text-blue-200`:`bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600`,`relative inline-flex items-center px-4 py-2 border text-sm font-medium`])},c(e),11,Ne))),128)),m(`button`,{onClick:a[4]||=e=>Y(G.value.current_page+1),disabled:!G.value.next_page_url,class:`relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed`},[...a[31]||=[m(`span`,{class:`sr-only`},`Next`,-1),m(`svg`,{class:`h-5 w-5`,fill:`currentColor`,viewBox:`0 0 20 20`},[m(`path`,{"fill-rule":`evenodd`,d:`M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z`,"clip-rule":`evenodd`})],-1)]],8,Pe)])])])])])):h(``,!0)]))]),a[32]||=m(`footer`,{class:`bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-16`},[m(`div`,{class:`max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8`},[m(`p`,{class:`text-center text-sm text-gray-500 dark:text-gray-400`},` Powered by Emeriosoft `)])],-1)])}}};var H=Fe;export{H as default};