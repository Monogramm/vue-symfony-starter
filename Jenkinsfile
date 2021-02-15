#!/usr/bin/env groovy

pipeline {
    agent any
    options {
        buildDiscarder(logRotator(numToKeepStr: '5'))
    }
    parameters {
        string(name: 'DOCKER_REPO', defaultValue: 'monogramm/vue-symfony-starter', description: 'Docker Image name.')

        string(name: 'DOCKER_TAG', defaultValue: 'latest', description: 'Docker Image tag.')

        choice(name: 'VARIANT', choices: ['alpine', 'debian'], description: 'Docker Image variant.')

        string(name: 'DOCKER_REGISTRY', defaultValue: 'registry.hub.docker.com', description: 'Docker Registry to publish the result image.')

        credentials(name: 'DOCKER_CREDENTIALS', credentialType: 'Username with password', required: true, defaultValue: 'dh-reg-ci', description: 'Docker credentials to push on the Docker registry.')

        choice(name: 'APP_PUBLIC_URL', choices: ['https://app.example.com'], description: 'Application target domain name.')

        choice(name: 'WEBSITE_PUBLIC_URL', choices: ['https://www.example.com'], description: 'Website target domain name.')

        choice(name: 'STORIES', choices: ['true', 'false'], description: 'Build Storybook in build/storybook?')
    }
    triggers {
        cron('H 6 * * 1-5')
    }
    stages {
        stage('pending') {
            steps {
                updateGitlabCommitStatus name: 'jenkins', state: 'pending'
            }
        }

        stage('check docker') {
            steps {
                sh "docker --version"
                sh "docker-compose --version"
            }
        }

        stage('build') {
            steps {
                updateGitlabCommitStatus name: 'jenkins', state: 'running'

                script {
                    docker.withRegistry("https://${DOCKER_REGISTRY}", "${DOCKER_CREDENTIALS}") {
                        def customImage = docker.build(
                            "${DOCKER_REGISTRY}/${DOCKER_REPO}:${DOCKER_TAG}",
                            "--build-arg TAG=${DOCKER_TAG} --build-arg STORIES=${STORIES} --build-arg APP_PUBLIC_URL=${APP_PUBLIC_URL} --build-arg WEBSITE_PUBLIC_URL=${WEBSITE_PUBLIC_URL} --build-arg VCS_REF=$(git rev-parse --short HEAD) --build-arg BUILD_DATE=$(date -u +'%Y-%m-%dT%H:%M:%SZ') -f Dockerfile.${VARIANT} ."
                        )

                        customImage.push()
                        customImage.push("${VARIANT}")
                    }
                }
            }
        }
    }
    post {
        always {
            // Always cleanup after the build.
            sh 'docker image prune -f --filter until=$(date -d "yesterday" +%Y-%m-%d)'
        }
        success {
            updateGitlabCommitStatus name: 'jenkins', state: 'success'
        }
        failure {
            updateGitlabCommitStatus name: 'jenkins', state: 'failed'
        }
    }
}
