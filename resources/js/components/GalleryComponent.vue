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
                                <img v-if="image.exception" :src="'storage/error.svg'" :alt="`Image Uplaoder ${index}`">
                                <p v-if="image.exception">{{ image.text }}</p>
                                <img v-else :src="'images/' + image.name" :alt="`Image Uplaoder ${index}`">
                                <div v-show="mouserOverIndex === index" class="details">
                                    <span>
                                        <button v-if="!image.exception" class="btn btn-info" @click="openModal(image.name)" data-toggle="modal"
                                                data-target="#exampleModal">
                                                <i class="fa fa-search"></i>
                                        </button>
                                        <button class="btn btn-danger" @click="remove(index, image.id)"><i
                                            class="fa fa-trash"></i></button>
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
                this.images.push({
                    text: 'File type not supported. - '+file.name,
                    exception: true,
                })
            }
            else if(file.size>10485760){
                this.images.push({
                    text: 'File size exceeded. - '+file.name,
                    exception: true,
                })
                console.log(`${file.name} !is not an image!!`)
            }
            else{
                this.files.push(file);
            }
        },
        getUserImages() {
            axios.get('/list')
                .then(response => {
                    this.images = response.data;
                })
        },
        upload() {
            this.files.forEach(file => {
                const formData = new FormData();
                formData.append('files', file, file.name);
                const config = {
                    onUploadProgress: function(progressEvent) {
                        let percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total)
                        console.log(percentCompleted)
                    }
                }
                axios.post('/store', formData, config)
                    .then(response => {
                        this.images.push(response.data.files);
                        this.files = [];
                    })
                    .catch(error => {
                        console.log(error)
                    })
            });
        },
        remove(index, id) {
            if(id) {
                axios.delete(
                    '/image/' + id)
                    .then(response => {
                        this.images.splice(index, 1);
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            }
            else{
                this.images.splice(index, 1);
            }
        },
    },
    mounted() {
        this.getUserImages();
    }
}
</script>

<style lang="scss" scoped>

.images-preview {
    display: flex;
    flex-wrap: wrap;
    margin-top: 20px;

    .img-wrapper {
        width: 209px;
        display: flex;
        flex-direction: column;
        margin: 10px;
        height: 209px;
        justify-content: center;
        box-shadow: 5px 5px 20px #3e3737;
        text-align: center;
    }

    .details {
        position: absolute;
        justify-content: center;
        align-self: center;
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

    &.dragging {
        background: #fff;
        color: #2196F3;
        border: 3px dashed #2196F3;
    }

    i {
        font-size: 85px;
    }
}
</style>
