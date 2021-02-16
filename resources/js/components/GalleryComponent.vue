<template>
    <div class="container">
        <div class="modal fade" tabindex="-1" role="dialog" id=exampleModal>
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <img v-bind:src="'images/'+ modalImage " class="img-fluid" v-if="modalImage!=null">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
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
                                 @mouseleave="mouserOverIndex = null">
                                <img :src="'images/' + image.name" :alt="`Image Uplaoder ${index}`">
                                <div v-show="mouserOverIndex === index" class="details">
                                    <span>
                                        <button class="btn btn-info" @click="openModal(image.name)" data-toggle="modal"
                                                data-target="#exampleModal">
                                                <i class="fa fa-search"></i>
                                        </button>
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
        mouserOverIndex: null,
        modalImage: null,
    }),
    methods: {
        openModal(imageName) {
            this.modalImage = imageName;
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
        justify-content: center;
        background: #ffffff;
        box-shadow: 5px 5px 20px #3e3737;

        img {
            justify-content: center;
        }

        .fullimage {
            max-height: 100%;
        }
    }

    .details {
        display: flex;
        flex-direction: column;
        align-items: self-start;
        margin: auto;
        transform: translate(0%, -150%);
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
