import{B as e,C as t,E as n,F as r,H as i,P as a,d as o,e as s,z as c}from"./index-BewjDgXt.js";import{b as l}from"./BarcodeScannerIntegration-OraT0pHr.js";const u={class:`barcode-test-page`},d={class:`flex-1 p-4 sm:p-6 lg:p-8`},f={class:`max-w-4xl mx-auto`},p={class:`grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6`},m={class:`test-card`},h={class:`barcode-container`},g={class:`test-info`},_={class:`test-card`},v={class:`barcode-container`},y={class:`test-info`},b={class:`test-card`},x={class:`barcode-container`},S={class:`test-info`},C={class:`test-card`},w={class:`barcode-container`},T={class:`test-info`},E={class:`test-card`},D={class:`barcode-container`},O={class:`test-info`},k={class:`test-card`},A={class:`test-info`},j={class:`mt-8`},M={__name:`BarcodeTestPage`,setup(s){let M=o(),N=a(null),P=a(null),F=a(null),I=a(null),L=a(null),R=a(null),z=()=>{M.back()},B=()=>{R.value&&R.value.openScanner()},V=e=>{console.log(`Scan result:`,e),alert(`Barcode scanned: ${e.code} (${e.format})`)},H=e=>{console.log(`Search action:`,e),alert(`Searching for: ${e.code}`)},U=e=>{console.log(`Create action:`,e),alert(`Creating item with barcode: ${e.code}`)},W=async e=>{try{await navigator.clipboard.writeText(e),alert(`Copied to clipboard: ${e}`)}catch(t){console.error(`Failed to copy:`,t);let n=document.createElement(`textarea`);n.value=e,document.body.appendChild(n),n.select(),document.execCommand(`copy`),document.body.removeChild(n),alert(`Copied to clipboard: ${e}`)}},G=()=>{N.value&&(N.value.innerHTML=`
      <div class="qr-placeholder">
        <div class="qr-grid">
          <div class="qr-square"></div>
          <div class="qr-square filled"></div>
          <div class="qr-square"></div>
          <div class="qr-square filled"></div>
          <div class="qr-square filled"></div>
          <div class="qr-square"></div>
          <div class="qr-square filled"></div>
          <div class="qr-square"></div>
          <div class="qr-square"></div>
        </div>
        <div class="qr-text">COLD-STORAGE-001</div>
      </div>
    `),P.value&&(P.value.innerHTML=`
      <div class="code128-placeholder">
        <div class="code128-lines">
          <div class="line thick"></div>
          <div class="line thin"></div>
          <div class="line thick"></div>
          <div class="line thin"></div>
          <div class="line thick"></div>
          <div class="line thin"></div>
          <div class="line thick"></div>
          <div class="line thin"></div>
          <div class="line thick"></div>
          <div class="line thin"></div>
          <div class="line thick"></div>
        </div>
        <div class="code128-text">CS2024001</div>
      </div>
    `),F.value&&(F.value.innerHTML=`
      <div class="ean13-placeholder">
        <div class="ean13-lines">
          <div class="line thick"></div>
          <div class="line thin"></div>
          <div class="line thick"></div>
          <div class="line thin"></div>
          <div class="line thick"></div>
          <div class="line thin"></div>
          <div class="line thick"></div>
          <div class="line thin"></div>
          <div class="line thick"></div>
          <div class="line thin"></div>
          <div class="line thick"></div>
        </div>
        <div class="ean13-text">1234567890123</div>
      </div>
    `),I.value&&(I.value.innerHTML=`
      <div class="upc-placeholder">
        <div class="upc-lines">
          <div class="line thick"></div>
          <div class="line thin"></div>
          <div class="line thick"></div>
          <div class="line thin"></div>
          <div class="line thick"></div>
          <div class="line thin"></div>
          <div class="line thick"></div>
          <div class="line thin"></div>
          <div class="line thick"></div>
          <div class="line thin"></div>
          <div class="line thick"></div>
        </div>
        <div class="upc-text">012345678905</div>
      </div>
    `),L.value&&(L.value.innerHTML=`
      <div class="code39-placeholder">
        <div class="code39-lines">
          <div class="line thick"></div>
          <div class="line thin"></div>
          <div class="line thick"></div>
          <div class="line thin"></div>
          <div class="line thick"></div>
          <div class="line thin"></div>
          <div class="line thick"></div>
          <div class="line thin"></div>
          <div class="line thick"></div>
          <div class="line thin"></div>
          <div class="line thick"></div>
        </div>
        <div class="code39-text">COLDSTORAGE</div>
      </div>
    `)};return r(()=>{G()}),(r,a)=>(i(),e(`div`,u,[c(`nav`,{class:`bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700`},[c(`div`,{class:`w-full px-4 sm:px-6 lg:px-8`},[c(`div`,{class:`flex justify-between items-center h-16`},[c(`button`,{onClick:z,class:`flex items-center space-x-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition-colors duration-200`},[...a[6]||=[c(`svg`,{class:`w-5 h-5`,fill:`none`,viewBox:`0 0 24 24`,stroke:`currentColor`},[c(`path`,{"stroke-linecap":`round`,"stroke-linejoin":`round`,"stroke-width":`2`,d:`M15 19l-7-7 7-7`})],-1),c(`span`,null,`Back`,-1)]]),a[8]||=c(`h1`,{class:`text-lg font-semibold text-gray-900 dark:text-gray-100`},` Barcode Test Page `,-1),c(`button`,{onClick:B,class:`flex items-center space-x-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200`},[...a[7]||=[c(`svg`,{class:`w-5 h-5`,fill:`none`,viewBox:`0 0 24 24`,stroke:`currentColor`},[c(`path`,{"stroke-linecap":`round`,"stroke-linejoin":`round`,"stroke-width":`2`,d:`M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z`})],-1),c(`span`,null,`Test Scanner`,-1)]])])])]),c(`div`,d,[c(`div`,f,[a[27]||=t(`<div class="mb-8 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6" data-v-b5b6acd2><h2 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-2" data-v-b5b6acd2> 🧪 Testing Instructions </h2><div class="text-blue-800 dark:text-blue-200 space-y-2" data-v-b5b6acd2><p data-v-b5b6acd2><strong data-v-b5b6acd2>1. Camera Testing:</strong> Use your phone&#39;s camera to scan the barcodes below</p><p data-v-b5b6acd2><strong data-v-b5b6acd2>2. Manual Testing:</strong> Copy the barcode numbers and paste them in the manual input field</p><p data-v-b5b6acd2><strong data-v-b5b6acd2>3. Mobile Testing:</strong> Test on both desktop and mobile devices</p><p data-v-b5b6acd2><strong data-v-b5b6acd2>4. Feature Testing:</strong> Try all scanner features (search, create, copy, etc.)</p></div></div>`,1),c(`div`,p,[c(`div`,m,[a[11]||=c(`h3`,{class:`test-title`},`QR Code Test`,-1),c(`div`,h,[c(`div`,{ref_key:`qrCodeElement`,ref:N,class:`qr-code`},null,512)]),c(`div`,g,[a[9]||=c(`p`,{class:`test-code`},`COLD-STORAGE-001`,-1),a[10]||=c(`p`,{class:`test-format`},`QR Code`,-1),c(`button`,{onClick:a[0]||=e=>W(`COLD-STORAGE-001`),class:`copy-button`},` Copy Code `)])]),c(`div`,_,[a[14]||=c(`h3`,{class:`test-title`},`Code 128 Test`,-1),c(`div`,v,[c(`div`,{ref_key:`code128Element`,ref:P,class:`code128-barcode`},null,512)]),c(`div`,y,[a[12]||=c(`p`,{class:`test-code`},`CS2024001`,-1),a[13]||=c(`p`,{class:`test-format`},`Code 128`,-1),c(`button`,{onClick:a[1]||=e=>W(`CS2024001`),class:`copy-button`},` Copy Code `)])]),c(`div`,b,[a[17]||=c(`h3`,{class:`test-title`},`EAN-13 Test`,-1),c(`div`,x,[c(`div`,{ref_key:`ean13Element`,ref:F,class:`ean13-barcode`},null,512)]),c(`div`,S,[a[15]||=c(`p`,{class:`test-code`},`1234567890123`,-1),a[16]||=c(`p`,{class:`test-format`},`EAN-13`,-1),c(`button`,{onClick:a[2]||=e=>W(`1234567890123`),class:`copy-button`},` Copy Code `)])]),c(`div`,C,[a[20]||=c(`h3`,{class:`test-title`},`UPC-A Test`,-1),c(`div`,w,[c(`div`,{ref_key:`upcElement`,ref:I,class:`upc-barcode`},null,512)]),c(`div`,T,[a[18]||=c(`p`,{class:`test-code`},`012345678905`,-1),a[19]||=c(`p`,{class:`test-format`},`UPC-A`,-1),c(`button`,{onClick:a[3]||=e=>W(`012345678905`),class:`copy-button`},` Copy Code `)])]),c(`div`,E,[a[23]||=c(`h3`,{class:`test-title`},`Code 39 Test`,-1),c(`div`,D,[c(`div`,{ref_key:`code39Element`,ref:L,class:`code39-barcode`},null,512)]),c(`div`,O,[a[21]||=c(`p`,{class:`test-code`},`COLDSTORAGE`,-1),a[22]||=c(`p`,{class:`test-format`},`Code 39`,-1),c(`button`,{onClick:a[4]||=e=>W(`COLDSTORAGE`),class:`copy-button`},` Copy Code `)])]),c(`div`,k,[a[26]||=t(`<h3 class="test-title" data-v-b5b6acd2>Custom Test</h3><div class="barcode-container" data-v-b5b6acd2><div class="custom-barcode" data-v-b5b6acd2><div class="barcode-lines" data-v-b5b6acd2><div class="line thick" data-v-b5b6acd2></div><div class="line thin" data-v-b5b6acd2></div><div class="line thick" data-v-b5b6acd2></div><div class="line thin" data-v-b5b6acd2></div><div class="line thick" data-v-b5b6acd2></div><div class="line thin" data-v-b5b6acd2></div><div class="line thick" data-v-b5b6acd2></div><div class="line thin" data-v-b5b6acd2></div><div class="line thick" data-v-b5b6acd2></div><div class="line thin" data-v-b5b6acd2></div><div class="line thick" data-v-b5b6acd2></div></div><div class="barcode-text" data-v-b5b6acd2>TEST123456</div></div></div>`,2),c(`div`,A,[a[24]||=c(`p`,{class:`test-code`},`TEST123456`,-1),a[25]||=c(`p`,{class:`test-format`},`Custom`,-1),c(`button`,{onClick:a[5]||=e=>W(`TEST123456`),class:`copy-button`},` Copy Code `)])])]),a[28]||=t(`<div class="mt-8 bg-gray-50 dark:bg-gray-800 rounded-lg p-6" data-v-b5b6acd2><h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4" data-v-b5b6acd2> ✅ Testing Checklist </h3><div class="grid grid-cols-1 md:grid-cols-2 gap-4" data-v-b5b6acd2><div class="space-y-2" data-v-b5b6acd2><h4 class="font-medium text-gray-700 dark:text-gray-300" data-v-b5b6acd2>Camera Features</h4><label class="flex items-center space-x-2" data-v-b5b6acd2><input type="checkbox" class="rounded" data-v-b5b6acd2><span class="text-sm text-gray-600 dark:text-gray-400" data-v-b5b6acd2>Camera access works</span></label><label class="flex items-center space-x-2" data-v-b5b6acd2><input type="checkbox" class="rounded" data-v-b5b6acd2><span class="text-sm text-gray-600 dark:text-gray-400" data-v-b5b6acd2>Flashlight toggle works</span></label><label class="flex items-center space-x-2" data-v-b5b6acd2><input type="checkbox" class="rounded" data-v-b5b6acd2><span class="text-sm text-gray-600 dark:text-gray-400" data-v-b5b6acd2>Camera switching works</span></label><label class="flex items-center space-x-2" data-v-b5b6acd2><input type="checkbox" class="rounded" data-v-b5b6acd2><span class="text-sm text-gray-600 dark:text-gray-400" data-v-b5b6acd2>Barcode detection works</span></label></div><div class="space-y-2" data-v-b5b6acd2><h4 class="font-medium text-gray-700 dark:text-gray-300" data-v-b5b6acd2>Manual Input</h4><label class="flex items-center space-x-2" data-v-b5b6acd2><input type="checkbox" class="rounded" data-v-b5b6acd2><span class="text-sm text-gray-600 dark:text-gray-400" data-v-b5b6acd2>Manual input works</span></label><label class="flex items-center space-x-2" data-v-b5b6acd2><input type="checkbox" class="rounded" data-v-b5b6acd2><span class="text-sm text-gray-600 dark:text-gray-400" data-v-b5b6acd2>Copy to clipboard works</span></label><label class="flex items-center space-x-2" data-v-b5b6acd2><input type="checkbox" class="rounded" data-v-b5b6acd2><span class="text-sm text-gray-600 dark:text-gray-400" data-v-b5b6acd2>Search action works</span></label><label class="flex items-center space-x-2" data-v-b5b6acd2><input type="checkbox" class="rounded" data-v-b5b6acd2><span class="text-sm text-gray-600 dark:text-gray-400" data-v-b5b6acd2>Create action works</span></label></div></div></div>`,1),c(`div`,j,[n(l,{ref_key:`scannerRef`,ref:R,"on-scan":V,"on-search":H,"on-create":U},null,512)])])])]))}};var N=s(M,[[`__scopeId`,`data-v-b5b6acd2`]]);export{N as default};