<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><strong>Gallery</strong></div>
                    <div class="card-body">
                        <div class="uploader"
                             @click="OnClink"
                             @dragenter="OnDragEnter"
                             @dragleave="OnDragLeave"
                             @dragover.prevent
                             @drop="onDrop"
                             :class="{ dragging: isDragging }">
                            <div>
                                <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                                <p>Drop file here or click to upload...</p>
                                <div class="file-input">
                                    <input type="file" id="file" @change="onInputChange" style="display:none" multiple/>
                                </div>
                            </div>
                        </div>
                        <div class="images-preview" v-show="images.length">
                            <div class="img-wrapper" v-for="(image, index) in images" :key="index"
                                 @mouseover="mouserOverIndex = index"
                                 @mouseleave="OnMouseLeave">
                                <img :src="'images/' + image.name" :alt="`Image Uplaoder ${index}`">
                                <div v-show="mouserOverIndex === index" class="details">
                                    <span>
                                        <button class="btn btn-info" @click="showModal()"><i
                                            class="fa fa-search"></i></button>
                                        <button class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data: () => ({
        isDragging: false,
        dragCount: 0,
        files: [],
        images: [],
        isMouseOver: false,
        mouserOverIndex: null,
        isModalVisible: false
    }),
    methods: {
        showModal() {
            this.isModalVisible = true;
        },
        closeModal() {
            this.isModalVisible = false;
        },
        ShowImage(index) {
            console.log(this.images[index]);
        },
        OnMouseOver(index) {
            console.log(index);
            this.isMouseOver = true;
        },
        OnMouseLeave(e) {
            // this.mouserOverIndex = null
            // this.isMouseOver = false;
        },
        OnClink(e) {
            $('#file').trigger('click');
        },
        OnDragEnter(e) {
            e.preventDefault();

            this.dragCount++;
            this.isDragging = true;

            return false;
        },
        OnDragLeave(e) {
            e.preventDefault();
            this.dragCount--;

            if (this.dragCount <= 0)
                this.isDragging = false;
        },
        onInputChange(e) {
            const files = e.target.files;

            Array.from(files).forEach(file => this.addImage(file));

            this.upload();
        },
        onDrop(e) {
            e.preventDefault();
            e.stopPropagation();

            this.isDragging = false;

            const files = e.dataTransfer.files;
            Array.from(files).forEach(file => this.addImage(file));

            this.upload();
        },
        addImage(file) {
            if (!file.type.match('image.*')) {
                this.$toastr.e(`${file.name} is not an image`);
                return;
            }

            this.files.push(file);
        },
        getUserImages() {
            axios.get('/list').then(response => {
                this.images = response.data;
            })
        },
        getFileSize(size) {
            const fSExt = ['Bytes', 'KB', 'MB', 'GB'];
            let i = 0;

            while (size > 900) {
                size /= 1024;
                i++;
            }

            return `${(Math.round(size * 100) / 100)} ${fSExt[i]}`;
        },
        upload() {
            const formData = new FormData();

            this.files.forEach(file => {
                formData.append('files', file);
            });

            axios.post('/store', formData)
                .then(response => {
                    // this.$toastr.s('All images uplaoded successfully');
                    this.images.push(response.data.files);
                    this.files = [];
                })
                .catch(error => {
                    console.log(error)
                })
        }
    },
    mounted() {
        this.getUserImages();
        // console.log(this.images)
    }
}
</script>

<style lang="scss" scoped>

.images-preview {
    display: flex;
    flex-wrap: wrap;
    margin-top: 20px;

    .img-wrapper {
        width: 160px;
        display: flex;
        flex-direction: column;
        margin: 10px;
        height: 150px;
        justify-content: space-between;
        background: #fff;
        box-shadow: 5px 5px 20px #3e3737;

        img {
            max-height: 105px;
        }

        .fullimage {
            max-height: 100%;
        }

        .button {
            position: center;
        }
    }

    .details {
        font-size: 12px;
        background: #fff;
        color: #000;
        display: flex;
        flex-direction: column;
        align-items: self-start;
        margin: auto;

        .name {
            overflow: hidden;
            height: 18px;
        }
    }
}

.uploader {
    width: 100%;
    background: #ffffff;
    color: #505050;
    padding: 40px 15px;
    text-align: center;
    border-radius: 0px;
    border: 3px dashed #b8b8b8;
    font-size: 20px;
    position: relative;

    &.dragging {
        background: #fff;
        color: #2196F3;
        border: 3px dashed #2196F3;

        .file-input label {
            background: #2196F3;
            color: #fff;
        }
    }

    i {
        font-size: 85px;
    }

    //.file-input {
    //    width: 200px;
    //    margin: auto;
    //    height: 68px;
    //    position: relative;
    //
    //    label,
    //    input {
    //        background: #fff;
    //        color: #2196F3;
    //        width: 100%;
    //        position: absolute;
    //        left: 0;
    //        top: 0;
    //        padding: 10px;
    //        border-radius: 4px;
    //        margin-top: 7px;
    //        cursor: pointer;
    //    }
    //
    //    input {
    //        opacity: 0;
    //        z-index: -2;
    //    }
    //}

    .images-preview {
        display: flex;
        flex-wrap: wrap;
        margin-top: 20px;

        .img-wrapper {
            width: 160px;
            display: flex;
            flex-direction: column;
            margin: 10px;
            height: 150px;
            justify-content: space-between;
            background: #fff;
            box-shadow: 5px 5px 20px #3e3737;

            img {
                max-height: 105px;
            }
        }

        .details {
            font-size: 12px;
            background: #fff;
            color: #000;
            display: flex;
            flex-direction: column;
            align-items: self-start;
            padding: 3px 6px;

            .name {
                overflow: hidden;
                height: 18px;
            }
        }
    }

    .upload-control {
        position: absolute;
        width: 100%;
        background: #fff;
        top: 0;
        left: 0;
        border-top-left-radius: 7px;
        border-top-right-radius: 7px;
        padding: 10px;
        padding-bottom: 4px;
        text-align: right;

        button, label {
            background: #2196F3;
            border: 2px solid #03A9F4;
            border-radius: 3px;
            color: #fff;
            font-size: 15px;
            cursor: pointer;
        }

        label {
            padding: 2px 5px;
            margin-right: 10px;
        }
    }
}
</style>
