<template>
    <div class="page">
        <!-- <div class="item">这是富文本编辑器的页面</div> -->
        <!-- 左边卡片 -->
        <div class="page_left">
            <div class="article">
                <template v-for="(article, index) in articleList" :key="index">
                    <div :class="[index === 0 ? 'article_item0':'article_item', index === articleIndex ? 'article_active':'']" @click="selectArticele(index)">
                        <div class="article_item_title">
                            {{article.title}}
                        </div>
                        <el-image fit="fill" :src="article.content_source_url" v-if="article.content_source_url"></el-image>
                        <div class="no_content_source_url" v-if="!article.content_source_url"></div>

                    </div> 
                </template>
                
            </div>
            <div class="article_add" @click="addArticeleItem" v-if="articleList.length <= 8">
                <el-icon class="avatar-uploader-icon"><plus /></el-icon>
                <div style="font-size: 14px; color: #8c939d">
                    新建图文
                </div>
            </div>
        </div>
        <!-- 中间富文本编辑区 -->
        <div class="page_con">
            <div style="width: 88%">
                <el-input
                    v-model="articleList[articleIndex].title"
                    maxlength="64"
                    placeholder="请输入标题"
                    show-word-limit
                    size="large"
                    type="text"
                >
                </el-input>
            </div>

            <div style="margin-top: 18px; flex: 1; width: 88%">
                <!-- 富文本内容区 -->

                <vue-ueditor-wrap v-model="articleList[articleIndex].content" :config="myConfig"></vue-ueditor-wrap>
            </div>

            <div style="width: 88%" class="page_con_btns">
                <el-button type="success" @click="getKindergartenList">保存为草稿</el-button>
                <!-- <el-button>预览</el-button> -->
            </div>
        </div>
        <!-- 右边封面设置 -->
        <div class="page_right">
            <div>
                <div>
                    <span style="color: #727272">封面</span>
                    <span style="color: red">*</span>
                    <span style="color: #b4b4b4; margin-left: 12px">(建议尺寸900*383像素)</span>
                </div>
                <div style="margin-top: 5px">
                    <el-upload
                        class="avatar-uploader"
                        action=""
                        :http-request="uploadFile"
                        :show-file-list="false"
                        
                    >
                        <img v-if="articleList[articleIndex].content_source_url" :src="articleList[articleIndex].content_source_url" class="avatar" />
                        <el-icon v-else class="avatar-uploader-icon"><plus /></el-icon>
                        <div class="el-upload__text" v-if="!articleList[articleIndex].content_source_url">
                            选择封面
                        </div>
                    </el-upload>
                </div>
            </div>
            <div style="margin-top: 18px">
                <div>
                    <span>摘要</span>
                    <span style="color: #b4b4b4; margin-left: 7px">(选填)</span>
                </div>
                <div style="margin-top: 5px">
                    <el-input
                        v-model="articleList[articleIndex].digest"
                        :autosize="{ minRows: 4, maxRows: 4 }"
                        maxlength="120"
                        type="textarea"
                        placeholder="选填，摘要会在订阅号消息、转发链接文章外的场景显露，帮助读者快速了解内容，如不填则默认抓取正文前54个字"
                        show-word-limit
                    >
                    </el-input>
                </div>
            </div>
            <div style="margin-top: 18px">
                <div>
                    <span>作者</span>
                    <span style="color: #b4b4b4; margin-left: 7px">(选填)</span>
                </div>
                <div style="margin-top: 5px">
                    <el-input
                        v-model="articleList[articleIndex].author"
                        maxlength="8"
                        placeholder="请输入作者"
                        show-word-limit
                        type="text"
                    >
                    </el-input>
                </div>
            </div>
        </div>


        <el-dialog
            v-model="dialogVisible"
            title="选择幼儿园"
            width="550px"
        >
            <div>
                <el-checkbox
                    v-model="checkAll"
                    :indeterminate="isIndeterminate"
                    @change="handleCheckAllChange"
                    >
                    全选
                </el-checkbox>
                <el-checkbox-group
                    v-model="checkedCities"
                    @change="handleCheckedCitiesChange"
                >
                    <el-checkbox v-for="kindergarten in kindergartenList" :key="kindergarten.id" :label="kindergarten.id">
                        {{kindergarten.nickname}}
                    </el-checkbox>
                </el-checkbox-group>
            </div>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="dialogVisible = false">取消</el-button>
                    <el-button type="primary" @click="dialogVisible = false"
                    >确定</el-button>
                </span>
            </template>
        </el-dialog>
        <!-- 显示幼儿园列表弹窗 -->
    </div>
</template>

<script>
// import {elInput} from 'element-plus'
import { ElMessage } from 'element-plus'
import { Plus } from '@element-plus/icons-vue'
// import VueUeditorWrap from 'vue-ueditor-wrap'

import axios from '../utils/axios'
import { uploadApi, imgUrl, kindergartenListApi, ueditorUrl, insterArticele } from '../api/api'
export default {
    components: {
        Plus,
        // VueUeditorWrap
    },
    data() {
        return {
            title: '这是富文本编辑器的页面',
            articleList: [
                {
                    title: '【入园准备】2022年春季新小班入园通知', // 文章标题
                    author: '', // 作者
                    digest: '', // 图文消息的摘要
                    content: '<h2><img src="http://img.baidu.com/hi/jx2/j_0003.gif"/>Vue + UEditor + v-model双向绑定</h2>', // 图文消息的内容
                    content_source_url: '', //图文消息的原文链接地址
                    thumb_media_id: '', // 图文消息的封面图片素材id(url链接)
                    show_cover_pic: '', // 是否显示封面，0为false，即不显示，1为true，即显示(默认)
                    need_open_comment: '', // 是否打开评论，0不打开(默认)，1打开
                    only_fans_can_comment: '' //是否粉丝才可评论，0所有人可评论(默认)，1粉丝才可评论
                }
            ], // 文章列表
            articleIndex: 0,
            kindergartenList: [
                {id: 1, nickname:'城市美林'},
                {id:2, nickname: 'lalalal'},
                {id: 3, nickname:'城市美林'},
                {id:4, nickname: 'lalalal'},
                {id: 5, nickname:'城市美林'},
                {id:6, nickname: 'lalalal'},
                {id: 7, nickname:'城市美林'},
                {id:8, nickname: 'lalalal'}
            ], // 幼儿园列表
            dialogVisible: false,
            checkAll: false,
            isIndeterminate: false,
            checkedCities: [],
            myConfig: {
                // 编辑器不自动被内容撑高
                autoHeightEnabled: true,
                // 初始容器高度
                initialFrameHeight: 340,
                // 初始容器宽度
                initialFrameWidth: '100%',
                // 上传文件接口（这个地址是我为了方便各位体验文件上传功能搭建的临时接口，请勿在生产环境使用！！！）
                serverUrl: 'https://saastest.qzriji.com/richtexteditor/UEditor/php/controller.php?action=config',
                // UEditor 资源文件的存放路径，如果你使用的是 vue-cli 生成的项目，通常不需要设置该选项，vue-ueditor-wrap 会自动处理常见的情况
                UEDITOR_HOME_URL: ueditorUrl
            } // 富文本编辑器的配置
        }
    },
    methods: {
        handleAvatarSuccess(res, file) {
            console.log(res, file)
        },
        beforeAvatarUpload() {

        },
        // 上传文章的封面图片
        uploadFile(params) {
            let { file } = params
            console.log(file)
            var formData = new FormData();
            formData.append("dir_name", 'editor_demo');
            formData.append("file", file);


            axios.post(uploadApi, formData).then(res => {
                console.log(res)
                if (res.code === 1) {
                    console.log(res.data)

                    this.articleList[this.articleIndex].content_source_url = imgUrl + res.data.img 
                }
            })
        },
        // 选择文章（编辑）
        selectArticele(idx) {
            this.articleIndex = idx
        },
        // 新增新的一篇文章
        addArticeleItem() {
            this.articleList.push({
                title: '', // 文章标题
                author: '', // 作者
                digest: '', // 图文消息的摘要
                content: '', // 图文消息的内容
                content_source_url: '', //图文消息的原文链接地址
                thumb_media_id: '', // 图文消息的封面图片素材id(url链接)
                show_cover_pic: '', // 是否显示封面，0为false，即不显示，1为true，即显示(默认)
                need_open_comment: '', // 是否打开评论，0不打开(默认)，1打开
                only_fans_can_comment: '' //是否粉丝才可评论，0所有人可评论(默认)，1粉丝才可评论
            })

            this.articleIndex = this.articleList.length - 1
        },
        // 幼儿园公众号列表
        getKindergartenList() {
            axios.post(kindergartenListApi, {
                page: 1,
                limit: 1000
            }).then(res => {
                if (res.code == 1) {
                    console.log(res)
                    this.kindergartenList = res.data

                    this.dialogVisible = true
                //     if (res.code === 1) {

                //     }
                } else {
                    ElMessage({
                        message: res.msg,
                        type: 'error',
                        duration: 2000
                    })
                }
                
            })
        },
        handleCheckAllChange(val) {
            console.log(val)
            let checkedCities = []
            if (val) {
                this.kindergartenList.forEach(item => {
                    checkedCities.push(item.id)
                })
            }
            this.checkedCities = checkedCities
            // this.checkedCities.values = val ? this.kindergartenList : []
            this.isIndeterminate = false
        },
        handleCheckedCitiesChange(value) {
            const checkedCount = value.length
            console.log(value)
            this.checkAll = checkedCount === this.kindergartenList.length
            this.isIndeterminate = checkedCount > 0 && checkedCount < this.kindergartenList.length
        },
        insterArticeleBtn() {
            let _params = {
                member_array: this.checkedCities,
                article_array: this.articleList
            }

            axios.post(insterArticele, _params).then(res => {
                if (res.code == 1) {
                    console.log(res)
                } else {
                    ElMessage({
                        message: res.msg,
                        type: 'error',
                        duration: 2000
                    })
                }
            })
        }
    }
}
</script>

<style lang="less" scope>
.page {
    min-width: 955px;
    display: flex;
    .page_left {
        background: rgba(247, 247, 247);
        width: 18%;
        height: 100vh;
        display: flex;
        flex-direction: column;
        // justify-content: center;
        align-items: center;
        .article {
            width: 80%;
            border: 1px solid #ccc;
            margin-top: 20px;
            .article_item0 {
                position: relative;
                width: 100%;
                height: 142px;
                font-size: 14px;
                .article_item_title {
                    color: #fff;
                    position: absolute;
                    bottom: 0;
                    left: 0;
                    width: 100%;
                    // background: #000;
                    display: -webkit-box;
                    -webkit-box-orient: vertical;
                    -webkit-line-clamp: 1;
                    overflow: hidden;
                    word-break: break-all;
                    padding: 0 10px;
                    box-sizing: border-box;
                    line-height: 40px;
                    z-index: 10;
                }
                .el-image {
                    width: 100%;
                    height: 100%;
                    object-fit: contain;
                }
                .no_content_source_url {
                    width: 100%;
                    height: 100%;
                    background: #e1e1e1;
                }
            }

            .article_item {
                display: flex;
                height: 78px;
                align-items: center;
                justify-content: space-around;
                font-size: 14px;
                border-top: 1px solid #ccc;
                .article_item_title {
                    width: 118px;
                    color: rgba(50, 61, 80);
                    display: -webkit-box;
                    -webkit-box-orient: vertical;
                    -webkit-line-clamp: 2;
                    overflow: hidden;
                    word-break: break-all;
                }
                .el-image {
                    width: 60px;
                    height: 60px;
                }
                .no_content_source_url {
                    width: 60px;
                    height: 60px;
                    background: #e1e1e1;
                }
            }

            .article_active {
                border: 1px solid #07c160;
            }
        }

        .article_add {
            margin-top: 16px;
            height: 78px;
            width: 80%;
            background: rgba(255,255,255);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
    }
    .page_con {
        flex: 1;
        height: calc(100vh - 20px);
        display: flex;
        flex-direction: column;
        align-items: center;
        overflow: scroll;
        padding-top: 20px;
        // background: red;


        .page_con_btns {
            display: flex;
            justify-content: end;
            height: 68px;
            align-items: center;
        }
    }
    .page_right {
        width: 26%;
        height: 100vh;
        border-left: 1px solid #ccc;
        padding: 20px;
        box-sizing: border-box;
    }
    .item {
        color: red;
    }
}


.avatar-uploader .el-upload {
  border: 1px dashed #d9d9d9;
  border-radius: 6px;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  width: 264px;
  height: 108px;
}
.avatar-uploader .el-upload:hover {
  border-color: #409eff;
}
.el-upload--text {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.el-icon.avatar-uploader-icon {
  font-size: 28px;
  color: #8c939d;
//   width: 178px;
//   height: 178px;
  text-align: center;
}
.avatar {
  width: 264px;
  height: 108px;
  display: block;
}
</style>