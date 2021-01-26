pipeline {
    agent any

    environment {
        registryUrl = "https://registry.digitalservice.id"
        registryBaseImageTag = "registry.digitalservice.id/pikobar-tesmasif/tesmasif-api"
        registryImage = ""
        registryCredential = "registry_jenkins"
        CAPROVER_URL = "https://captain.rover.digitalservice.id"
        CAPROVER_APP = "tesmasif-api"
        SHORT_COMMIT = "${GIT_COMMIT[0..7]}"
    }

    stages {
        stage("linter") {
            agent {
                docker {
                    image 'cytopia/phpcs'
                    args '--entrypoint='
                }
            }
            steps {
                sh 'phpcs .'
            }
        }

        stage("build") {
            steps {
                script {
                    registryImage = docker.build registryBaseImageTag + ":$SHORT_COMMIT"
                }
            }
        }

        stage("deploy") {
            when { branch 'develop' }
            steps {
                script {
                    docker.withRegistry(registryUrl, registryCredential) {
                        registryImage.push()
                    }
                }

                script {
                    withCredentials([usernamePassword(credentialsId: "caprover_admin", usernameVariable: "CAP_USERNAME", passwordVariable: "CAP_PASSWORD")]) {
                        sh "docker run caprover/cli-caprover:v2.1.1 caprover deploy --caproverUrl $CAPROVER_URL --caproverPassword \"$CAP_PASSWORD\" --caproverApp $CAPROVER_APP --imageName $registryBaseImageTag:$SHORT_COMMIT"
                    }
                }
            }
        }

        stage("cleanup") {
            steps {
                sh "docker rmi $registryBaseImageTag:$SHORT_COMMIT"
            }
        }
    }
}
