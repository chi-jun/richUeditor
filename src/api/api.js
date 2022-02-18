
let baseUrl = process.env.NODE_ENV === 'development' ? '/api' : ''
export let ueditorUrl = process.env.NODE_ENV === 'development' ? '/UEditor/' : `/richtexteditor/UEditor/`

// 上传图片时的接口地址
export let uploadApi = baseUrl + '/admin/Service/uploadImg'
// 幼儿园公众号列表
export let kindergartenListApi = baseUrl + '/admin/DraftBox/getKindergarten.html'
// 新建保存公众号文章列表
export let insterArticele = baseUrl + '/admin/DraftBox/addDraftBox'


// 上传图片成功时前面添加的域名
export let imgUrl = 'https://saasimg.qzriji.com/'
