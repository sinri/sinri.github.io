---
layout: post
author: sinri
title: "Gradle, JReleaser, and Maven Central"
date: 2026-02-10
excerpt: "使用JReleaser将Gradle项目发布到Maven中央库的配置指南，涵盖GPG密钥、Sonatype账号、gradle.properties与build.gradle.kts的完整配置步骤。含 signing 与 JReleaser deploy 配置。"
---

# Gradle, JReleaser, and Maven Central

How to publish your Gradle project to Maven Central using JReleaser

## 0x01

* 准备好GPG密钥
* 注册Sonatype账号，并办妥域控制权
* 准备好相应的 Java Project
* 安装 Gradle (>=9.0.0)

## 0x02

> 需确保 `~/.jreleaser` 目录存在。

运行 `gpg --list-secret-keys --keyid-format LONG` 获取密钥信息。
输出里类似 `sec  rsa4096/****` 格式的字符串里`/`后面的一串内容是 `GPG_KEY_ID`。

使用下面的命令获取 `GPG_PUBLIC_KEY`：

`gpg --armor --export $GPG_KEY_ID`

使用下面的命令获取 `GPG_SECRET_KEY`：

`gpg --armor --export-secret-keys $GPG_KEY_ID`

编辑 `~/.jreleaser/config.yml`：

```yml
JRELEASER_GPG_PUBLIC_KEY: |
    # 此处是 GPG_PUBLIC_KEY 的完整内容
JRELEASER_GPG_SECRET_KEY: |
    # 此处是 GPG_SECRET_KEY 的完整内容
JRELEASER_GPG_PASSPHRASE: "此处是密钥的对应密码"
```

## 0x03

在 `~/.gradle/gradle.properties`，配置好

```properties
sonatypeUsername=***
sonatypePassword=***

signing.keyId=***
signing.password=***
```

## 0x04

在 Java 项目中，编辑好下面的文件，确保安装好 Gradlew 相关（IDEA 的话会根据下面的配置文件自动安装）。

### settings.gradle.kts

```kts
rootProject.name = "***"
```

### gradle.properties

```properties
# Project Properties
group=io.github.sinri
version=1.0.0
# Project Metadata
projectName=
projectDescription=
projectUrl=
projectScmUrl=
# License
licenseName=GPL-v3.0
licenseUrl=https://www.gnu.org/licenses/gpl-3.0.txt
# Developer Info
developerName=
developerEmail=
developerOrganization=
developerOrganizationUrl=
# Build Settings
org.gradle.jvmargs=-Xmx2g -XX:MaxMetaspaceSize=512m
org.gradle.parallel=true
org.gradle.caching=true
# GPG Signing Configuration
signing.gnupg.keyName=
signing.gnupg.executable=gpg
signing.gnupg.useLegacyGpg=false
# Denpendency Version
#   such as
# jspecifyVersion=1.0.0
```

### build.gradle.kts

这个版本的特殊指令：

* 引用依赖优先使用私有仓库，然后是 Maven 中央仓库。
* 发布时指定了仅正式版本号发布到 Maven 中央仓库，其他发布到私有仓库。
* 发布正式版本到 Maven 中央仓库时，运行 gradle 的 `publish` 命令会先打包，上传，然后直接发布并等待发布完成，不需要手工确认。

```kts
plugins {
    `java-library`
    `maven-publish`
    signing
    id("org.jreleaser") version "1.22.0"
}

// Project metadata from gradle.properties
group = property("group") as String
version = property("version") as String

val projectName: String by project
val projectDescription: String by project
val projectUrl: String by project
val projectScmUrl: String by project
val licenseName: String by project
val licenseUrl: String by project
val developerName: String by project
val developerEmail: String by project
val developerOrganization: String by project
val developerOrganizationUrl: String by project

val sonatypeUsername: String by project
val sonatypePassword: String by project

// Dependency versions
// val jspecifyVersion: String by project

repositories {
    // Internal Nexus repository for dependencies
    maven {
        name = "InternalNexus"
        url = uri(findProperty("internalNexusPublicUrl") as String)
        credentials {
            username = findProperty("internalNexusUsername") as String
            password = findProperty("internalNexusPassword") as String
        }
    }

    mavenCentral()
}

dependencies {
    // API dependency (transitive)
    // https://mvnrepository.com/artifact/org.jspecify/jspecify
    // compileOnly("org.jspecify:jspecify:$jspecifyVersion")
    // testCompileOnly("org.jspecify:jspecify:$jspecifyVersion")

    // Test dependencies
    // testImplementation("io.vertx:vertx-junit5:$vertxVersion")
    // testRuntimeOnly("org.junit.platform:junit-platform-launcher")
}

java {
    toolchain {
        languageVersion.set(JavaLanguageVersion.of(17))
    }
    withSourcesJar()
    withJavadocJar()
}

tasks.compileJava {
    options.encoding = "UTF-8"
    options.release.set(17)
    // Gradle will automatically compile Java modules
}

tasks.compileTestJava {
    options.encoding = "UTF-8"
    options.release.set(17)
}

// Configure resources (exclude config.properties like Maven)
tasks.processResources {
    exclude("config.properties")
}

// Configure test task (matching Maven surefire configuration)
tasks.test {
    useJUnitPlatform()
    include("**/**/**/*Test.class")
}

// Configure JavaDoc (matching Maven javadoc plugin configuration)
tasks.javadoc {
    options.encoding = "UTF-8"
    if (options is StandardJavadocDocletOptions) {
        val stdOptions = options as StandardJavadocDocletOptions
        stdOptions.charSet = "UTF-8"
        stdOptions.docEncoding = "UTF-8"
        stdOptions.memberLevel = JavadocMemberLevel.PROTECTED
        stdOptions.docTitle = "$projectName $version Document"
        stdOptions.windowTitle = "$projectName $version Document"
        stdOptions.addBooleanOption("html5", true)
        stdOptions.addStringOption("Xdoclint:-missing", "-quiet") // 提示缺失的注释
    }
}

// Publishing configuration
publishing {
    publications {
        create<MavenPublication>("mavenJava") {
            from(components["java"])
            // 显式绑定版本号，确保与项目版本一致
            version = project.version.toString()

            pom {
                name.set(projectName)
                description.set(projectDescription)
                url.set(projectUrl)

                licenses {
                    license {
                        name.set(licenseName)
                        url.set(licenseUrl)
                    }
                }

                developers {
                    developer {
                        name.set(developerName)
                        email.set(developerEmail)
                        organization.set(developerOrganization)
                        organizationUrl.set(developerOrganizationUrl)
                    }
                }

                scm {
                    url.set(projectScmUrl)
                }
            }
        }
    }

    repositories {
        maven {
            // name = "mixed"
            if (version.toString().endsWith("SNAPSHOT")) {
                url = uri(findProperty("internalNexusSnapshotsUrl") as String)
                credentials {
                    username = findProperty("internalNexusUsername") as String
                    password = findProperty("internalNexusPassword") as String
                }
            } else if (version.toString().contains(Regex("-[A-Za-z]+"))) {
                url = uri(findProperty("internalNexusReleasesUrl") as String)
                credentials {
                    username = findProperty("internalNexusUsername") as String
                    password = findProperty("internalNexusPassword") as String
                }
            } else {
                url = uri(layout.buildDirectory.dir("staging-deploy"))
            }
        }
    }
}

// 在 publishing 配置块之后添加
tasks.named("publish") {
    // 仅当版本是正式版本时，自动触发 jreleaserDeploy
    if (!version.toString().endsWith("SNAPSHOT") &&
        !version.toString().contains(Regex("-[A-Za-z]+"))
    ) {
        doFirst {
            logger.lifecycle(">>> Publishing release version $version")
            logger.lifecycle(">>> Will automatically deploy to Maven Central after staging")
        }
        finalizedBy("jreleaserDeploy")
    }
}

// Signing configuration
signing {
    // Use GnuPG command for signing (configured in gradle.properties)
    useGpgCmd()

    // Only sign if not a SNAPSHOT and signing credentials are available
    setRequired({
        !version.toString().endsWith("SNAPSHOT") && gradle.taskGraph.hasTask("publish")
    })
    sign(publishing.publications["mavenJava"])
}

// JReleaser 配置
jreleaser {
     signing {
         pgp {
             active.set(org.jreleaser.model.Active.ALWAYS)
             armored.set(true)
         }
     }
    deploy {
        maven {
            mavenCentral {
                active = org.jreleaser.model.Active.RELEASE
                register("sonatype") { // "sonatype" 为自定义名称
                    active.set(org.jreleaser.model.Active.ALWAYS)
                    // 如果使用新的 Central Portal (https://central.sonatype.com)
                    url.set("https://central.sonatype.com/api/v1/publisher")
                    // 指定制品暂存目录，JReleaser 会从这里读取 POM 和 JAR
                    stagingRepository("build/staging-deploy")

                    // 认证信息通常通过环境变量提供，或在这里显式设置
                    username.set(sonatypeUsername)
                    password.set(sonatypePassword)

                    enabled.set(true)
                }
            }
        }
    }
}
```