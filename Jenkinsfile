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
        stage("build") {
            steps {
                script {
                    registryImage = docker.build registryBaseImageTag + ":$SHORT_COMMIT"
                }
            }
        }

        stage("test") {
            steps {
                script {
                    registryImage.inside('--entrypoint=') {
                        sh 'cp .env-example .env'
                        sh 'composer install --no-progress'
                        sh 'php artisan key:generate --ansi'
                        sh 'php vendor/bin/phpcs --standard=phpcs.xml'
                        sh 'php vendor/bin/phpunit --configuration=phpunit.xml'
                    }
                }
            }
        }

        stage("deploy") {
            when {
                expression { env.BRANCH_NAME == 'develop' }
            }
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
