<?php

namespace App\Form;

use App\Entity\Candidate;
use App\Entity\Gender;
use App\Entity\User;
use DateTimeImmutable;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CandidateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'first_name',
                ],
                'label' => 'First name',
            ])

            ->add('lastName', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'last_name',
                ],
                'label' => 'Last name',
            ])

            ->add('gender', EntityType::class, [
                'class' => Gender::class,
                'choice_label' => 'name',
                'required' => false,
                'attr' => [
                    'id' => 'gender',
                ],
                'label' => 'Gender',
                'label_attr' => [
                    'class' => 'active',
                ],
                'placeholder' => 'Choose an option...',
            ])
            ->add('profilePictureFile', FileType::class,[
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '20M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Image document',
                    ])
                ],
                'attr' => [
                    'accept' => '.jpg,.jpeg,.png,.gif',
                    'id' => 'photo',
                ]
            ])

            ->add('currentLocation', TextType::class, [
                'required' => false,
                'attr' => [
                    'id' => 'current_location',
                ],
                'label' => 'current_location',
            ])

            ->add('address', TextType::class, [
                'required' => false,
                'attr' => [
                    'id' => 'address',
                ],
                'label' => 'address',
            ])

            ->add('country', TextType::class, [
                'required' => false,
                'attr' => [
                    'id' => 'country',
                ],
                'label' => 'Country',
            ])

            ->add('nationality', TextType::class, [
                'required' => false,
                'attr' => [
                    'id' => 'nationality',
                ],
                'label' => 'Nationality',
            ])

            ->add('birthDate', BirthdayType::class, [
                'required' => false,
                'label' => 'Birthdate',
                // 'widget' => 'single_text',
                'attr' => [
                    'class' => 'datepicker',
                    'id' => 'birth_date',
                ],
                'label_attr' => [
                    'class' => 'active',
                ],
                'format' => 'yyyy-MM-dd',
                
            ])

            ->add('birthPlace', TextType::class, [
                'required' => false,
                'label' => 'Birthplace',
                'attr' => [
                    'id' => 'birth_place',
                ],
            ])

            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => 'Short description for your profile, as well as more personnal informations (e.g. your hobbies/interests ). You can also paste any link you want.',
                'attr' => [
                    'id' => 'description',
                    'class' => 'materialize-textarea',
                    'cols' => 50,
                    'rows' => 10,
                ],
            ])




            ->addEventListener(FormEvents::POST_SUBMIT, $this->setUpdatedAt(...))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidate::class,
        ]);
    }


    private function setUpdatedAt(FormEvent $event): void
    {
        $candidate = $event->getData();
        $candidate->setUpdatedAt(new DateTimeImmutable());
    }
}
